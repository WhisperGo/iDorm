<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    public function create(Request $request) 
{
    $kategori = $request->get('kategori_fasilitas');
    $user = Auth::user();
    $gender = $user->residentDetails->gender; 

    // 1. Ambil data fasilitas berdasarkan kategori & gender
    $facilities = Facility::query()
        ->when($kategori, function($query) use ($kategori, $gender) {
            if ($kategori == 'mesin_cuci') {
                return $query->where('name', 'LIKE', "%Mesin Cuci $gender%");
            } 
            if ($kategori == 'cws') {
                return $query->where('name', 'LIKE', "%Co-Working Space%");
            } 
            if ($kategori == 'sergun') {
                return $query->where('name', 'LIKE', "%Serba Guna%");
            } 
            if ($kategori == 'theater') {
                // Mencari kata Theater atau Theatre (RE vs ER)
                return $query->where(function($q) {
                    $q->where('name', 'LIKE', '%Theater%')
                      ->orWhere('name', 'LIKE', '%Theatre%');
                });
            }
            if ($kategori == 'dapur') {
                return $query->where('name', 'LIKE', '%Dapur%');
            }
            
            // Jika kategori tidak dikenali, buat query yang pasti kosong
            return $query->where('id', 0);
        })
        ->get();

    // 2. Ambil data slot waktu (TAMBAHKAN BARIS INI)
    // Ini untuk mengisi dropdown per 2 jam kalau user pilih Mesin Cuci/Theatre
    $timeSlots = \App\Models\TimeSlot::where('facilities', 'heavy')->get();

    // 3. Ambil riwayat booking user
    $myBookings = \App\Models\Booking::where('user_id', $user->id)
        ->with(['facility', 'status', 'slot'])
        ->latest()
        ->get();

    // 4. Kirim 'timeSlots' ke View (TAMBAHKAN 'timeSlots' di compact)
    return view('penghuni.addBooking', compact('facilities', 'user', 'kategori', 'myBookings', 'timeSlots'));
}

    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $rules = [
            'facility_id'  => 'required|exists:facilities,id',
            'facility_id.*' => 'exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
        ];

        // 2. Logic Validasi Khusus
        // Jika ada slot_id (Mesin Cuci/Theatre), maka start/end time diambil dari tabel time_slots
        if ($request->has('slot_id') && $request->slot_id != null) {
            $rules['slot_id'] = 'required|exists:time_slots,id';
        } else {
            // Jika manual (Dapur/Sergun/CWS), start dan end time wajib diisi manual
            $rules['start_time'] = 'required';
            $rules['end_time']   = 'required|after:start_time';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $facilityId = $request->facility_id;

            if(is_array($facilityId)) {
                $facilityId = $facilityId[0]; // Ambil yang pertama jika array
            }

            if(!$facilityId) {
                return redirect()->back()->with('error', 'Silakan pilih minimal satu mesin!');
            }
            $data = [
                'user_id'     => Auth::id(),
                'facility_id' => $facilityId,
                'booking_date'=> $request->booking_date,
                'status_id'   => 1, // Status: Booked/Pending
                'cleanliness_status' => 'pending',
                'description' => $request->description ?? $request->keterangan ?? null,
            ];

            // 3. Logic Sesi Otomatis
            if ($request->slot_id) {
                // Ambil jam dari tabel time_slots (Untuk Mesin Cuci/Theatre)
                $slot = \App\Models\TimeSlot::find($request->slot_id);
                $data['slot_id']    = $slot->id;
                $data['start_time'] = $slot->start_time;
                $data['end_time']   = $slot->end_time;
            } else {
                // Ambil jam dari input manual (Untuk Dapur/Sergun/CWS)
                $data['slot_id']    = null;
                $data['start_time'] = $request->start_time;
                $data['end_time']   = $request->end_time;
            }

            $cekTabrakan = \App\Models\Booking::where('facility_id', $data['facility_id'])
            ->where('booking_date', $request->booking_date)
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })->exists();

            if ($cekTabrakan) {
                return redirect()->back()->with('error', 'Maaf, jam tersebut sudah dibooking orang lain!');
            }

            // Simpan ke Database
            \App\Models\Booking::create($data);

            DB::commit();
            return redirect()->route('booking.create', ['kategori_fasilitas' => $request->kategori])
                 ->with('success', 'Booking berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Ini biar datanya gak "hilang" misterius, tapi muncul errornya apa
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showSchedule(Request $request, $category)
    {
        $user = Auth::user();
        $userGender = Auth::user()->residentDetails->gender;
        $search = $request->get('search');
        
        // Konversi slug 'mesin-cuci' menjadi 'Mesin Cuci' untuk judul
        $title = ucwords(str_replace(['-', '_'], ' ', $category));

        // Ambil data booking untuk fasilitas terkait
        $query = \App\Models\Booking::with(['user.residentDetails', 'facility', 'status', 'slot']);
        // if ($user->role->role_name === 'Resident') {
        // $query->where('user_id', $user->id); 
        // }
    // Mapping Filter agar Schedule menampilkan data yang benar
    $query->whereHas('facility', function($q) use ($category, $userGender) {
        if ($category == 'mesin-cuci' || $category == 'mesin_cuci') {
            $q->where('name', 'LIKE', "%Mesin Cuci $userGender%");
        } elseif ($category == 'cws') {
            $q->where('name', 'LIKE', "%Co-Working%");
        } elseif ($category == 'sergun') {
            $q->where('name', 'LIKE', "%Serba Guna%");
        } elseif ($category == 'theater') {
            $q->where('name', 'LIKE', "%Theater%");
        } else {
            $q->where('name', 'LIKE', "%$category%");
        }
    });

    if ($search) {
        $query->whereHas('user.residentDetails', function($q) use ($search) {
            $q->where('full_name', 'LIKE', "%$search%");
        });
    }

    $bookings = $query->orderBy('booking_date', 'desc')
                      ->orderBy('start_time', 'asc')
                      ->paginate(10);

    return view('penghuni.viewSchedule', compact('bookings', 'title', 'category'));
    }

    // app/Http/Controllers/BookingController.php

    public function uploadPhoto(Request $request, Booking $booking)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);
    
        if ($request->hasFile('photo')) {
            // Simpan foto ke folder storage/app/public/cleanliness
            $path = $request->file('photo')->store('cleanliness', 'public');
            
            $booking->update([
                'photo_proof_path' => $path,
                'cleanliness_status' => 'pending' // Menunggu verifikasi admin
            ]);
    
            return back()->with('success', 'Foto kebersihan berhasil diunggah. Menunggu verifikasi admin.');
        }
    }

    
}
