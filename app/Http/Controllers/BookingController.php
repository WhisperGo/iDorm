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
    $gender = $user->residentDetails->gender ?? null; 

    // 1. Ambil data fasilitas berdasarkan kategori & gender
    $facilities = Facility::query()
        ->when($kategori, function($query) use ($kategori, $gender) {
            if ($kategori == 'mesin_cuci') {
                return $gender ? $query->where('name', 'LIKE', "%Mesin Cuci $gender%") : $query->where('name', 'LIKE', "%Mesin Cuci%");
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
        // dd($request->all());
        // 1. Validasi Dasar
        $rules = [
            'facility_id'  => 'required|exists:facilities,id',
            'facility_id.*' => 'exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'item_dapur'  => 'nullable|string',
        ];

        if ($request->kategori == 'cws') {
            $rules['jumlah_orang'] = 'required|numeric|min:20';
        }

        $validated = $request->validate($rules);

        if ($request->kategori == 'cws') {
            $date = \Carbon\Carbon::parse($request->booking_date);

            // A. Cek hari Kamis
            if ($date->isThursday()) {
                return redirect()->back()->with('error', 'Booking Co-Working Space tidak bisa dilakukan pada hari Kamis. Silakan pilih hari lain.')->withInput();
            }

            if ($request->jumlah_orang < 20) {
                return redirect()->back()->with('error', 'Jumlah orang untuk booking Co-Working Space minimal 20 orang.')->withInput();
            }
        }

        if($request->kategori == 'theater' || $request->kategori == 'theatre') {
            $rules['description'] = 'required|max:255';
            $rules['jumlah_orang'] = 'required|numeric|min:1|max:50';

            if ($request->jumlah_orang > 50) {
                return redirect()->back()->with('error', 'Jumlah orang untuk booking Theater maksimal 50 orang.')->withInput();
            }
        }

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

            // 1. Ambil semua ID fasilitas (bisa satu ID atau array ID mesin cuci)
            $facilityIds = is_array($request->facility_id) ? $request->facility_id : [$request->facility_id];

            if (empty($facilityIds) || !$facilityIds[0]) {
                return redirect()->back()->with('error', 'Silakan pilih fasilitas/mesin!');
            }

            // 2. Looping untuk setiap fasilitas yang dipilih
            foreach ($facilityIds as $fId) {
                
                // Siapkan data dasar
                $data = [
                    'user_id'     => Auth::id(),
                    'facility_id' => $fId,
                    'booking_date'=> $request->booking_date,
                    'status_id'   => 1, 
                    'cleanliness_status' => 'pending',
                    'description' => $request->description ?? $request->keterangan ?? null,
                    'item_dapur'  => $request->item_dapur,
                    'item_sergun' => $request->item_sergun,
                ];

                // Logic Sesi Waktu (Sama untuk semua mesin dalam satu booking)
                if ($request->slot_id) {
                    $slot = \App\Models\TimeSlot::find($request->slot_id);
                    $data['slot_id']    = $slot->id;
                    $data['start_time'] = $slot->start_time;
                    $data['end_time']   = $slot->end_time;
                } else {
                    $data['slot_id']    = null;
                    $data['start_time'] = $request->start_time;
                    $data['end_time']   = $request->end_time;
                }

                // 3. Cek Tabrakan PER MESIN/ALAT
                $cekTabrakan = \App\Models\Booking::where('facility_id', $fId)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status_id', [1, 4])
                    ->where(function($query) use ($data) {
                        if (!empty($data['item_dapur'])) {
                            $query->where('item_dapur', $data['item_dapur']);
                        } elseif (!empty($data['item_sergun'])) {
                            $query->where('item_sergun', $data['item_sergun']);
                        }
                    })
                    ->where(function($query) use ($data) {
                        $query->where('start_time', '<', $data['end_time'])
                                ->where('end_time', '>', $data['start_time']);
                    })->exists();

                if ($cekTabrakan) {
                    $namaFasilitas = \App\Models\Facility::find($fId)->name;
                    $jamMulai = substr($data['start_time'], 0, 5);
                    $jamSelesai = substr($data['end_time'], 0, 5);
                    
                    DB::rollBack(); // Batalkan semua jika ada satu yang tabrakan
                    return redirect()->route('booking.create', ['kategori_fasilitas' => $request->kategori])
                        ->with('error', "Waduh! $namaFasilitas jam $jamMulai-$jamSelesai sudah dibooking. Pilih mesin/waktu lain!")
                        ->withInput();
                }

                // 4. Simpan ke Database (Akan dijalankan sebanyak jumlah mesin yang dicentang)
                \App\Models\Booking::create($data);
            }

            DB::commit();
            return redirect()->route('booking.myBookings')->with('success', 'Booking berhasil dibuat! Silakan cek jadwal kamu di sini.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Ini biar datanya gak "hilang" misterius, tapi muncul errornya apa
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showSchedule(Request $request, $category)
    {
        $user = Auth::user();
        // Karena admin dan manager tidak terpaku kepada gender maka ?? null digunakan untuk menghandle error tersebut
        $userGender = $user->residentDetails->gender ?? null;
        
        $search = $request->get('search');
        $itemFilter = $request->get('item');
        
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
                if($userGender){
                    $q->where('name', 'LIKE', "%Mesin Cuci $userGender%");
                } else {
                    $q->where('name', 'LIKE', "%Mesin Cuci%");
                }
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

    // --- TAMBAHKAN LOGIKA FILTER ALAT DI SINI ---
    if ($category == 'dapur' && $itemFilter) {
        $query->where('item_dapur', $itemFilter);
    }
    
    // Filter Area Sergun (Opsional, sekalian biar konsisten)
    if ($category == 'sergun' && $itemFilter) {
        $query->where('item_sergun', $itemFilter);
    }

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
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('cleanliness', 'public');

            $booking->update([
                'photo_proof_path' => $path,
                'cleanliness_status' => 'pending'
            ]);

            return back()->with('success', 'Foto kebersihan berhasil diunggah.');
        }
    }

    public function myPersonalHistory()
    {
        $user = Auth::user();

        // Ambil hanya booking milik user ini, urutkan dari yang terbaru
        $myBookings = \App\Models\Booking::where('user_id', $user->id)
            ->with(['facility', 'status', 'slot'])
            ->latest()
            ->get();

        return view('penghuni.myBookings', compact('myBookings'));
    }
}
