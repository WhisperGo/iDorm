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
        // Ambil kategori dari request URL
        $kategori = $request->get('kategori_fasilitas');

        $user = Auth::user();
        $gender = $user->residentDetails->gender ?? null;

        // 1. Ambil data fasilitas berdasarkan kategori & gender
        $facilities = Facility::query()
            ->when($kategori, function ($query) use ($kategori, $gender) {

                if ($kategori == 'mesin_cuci') {
                    return $gender
                        ? $query->where('name', 'LIKE', "%Mesin Cuci $gender%")
                        : $query->where('name', 'LIKE', "%Mesin Cuci%");
                }

                if ($kategori == 'cws') {
                    return $query->where('name', 'LIKE', "%Co-Working Space%");
                }

                if ($kategori == 'sergun') {
                    return $query->where('name', 'LIKE', '%Serba%');
                    // return $query->where('name', 'LIKE', "%Serbaguna%");
                }

                if ($kategori == 'theater') {
                    // Mencari kata Theater atau Theatre (RE vs ER)
                    return $query->where('name', 'LIKE', '%Theat%');
                }
                if ($kategori == 'dapur') {
                    return $query->where('name', 'LIKE', '%Dapur%');
                }

                // Jika kategori tidak dikenali, buat query yang pasti kosong
                return $query->where('id', 0);
            })
            ->get();
            
        $items = collect();
        if ($facilities->isNotEmpty()) {
            $items = \App\Models\FacilityItem::where('facility_id', $facilities->first()->id)->get();
        }
        // if($facilities == 'theater' || $facilities == 'sergun'){
        //     dd($facilities);
        // }

        // 2. Ambil data slot waktu (TAMBAHKAN BARIS INI)
        // Ini untuk mengisi dropdown per 2 jam kalau user pilih Mesin Cuci/Theatre
        $timeSlots = TimeSlot::where('facilities', 'heavy')->get();

        // 3. Ambil riwayat booking user
        $myBookings = Booking::where('user_id', $user->id)
            ->with(['facility', 'status', 'slot'])
            ->latest()
            ->get();

        // 4. Kirim 'timeSlots' ke View (TAMBAHKAN 'timeSlots' di compact)
        return view('penghuni.addBooking', compact('facilities', 'user', 'kategori', 'myBookings', 'timeSlots', 'items'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // 1. Validasi Dasar
        $rules = [
            'facility_id' => 'required|exists:facilities,id',
            'facility_id.*' => 'exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'item_dapur' => 'nullable|string',
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

        if ($request->kategori == 'theater' || $request->kategori == 'theatre') {
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
            $rules['end_time'] = 'required|after:start_time';
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
                    'user_id' => Auth::id(),
                    'facility_id' => $fId,
                    'booking_date' => $request->booking_date,
                    'status_id' => 1,
                    'cleanliness_status' => 'pending',
                    // GABUNGKAN info alat/area ke dalam description karena kolom item_dapur/sergun sudah dihapus
                    'description' => ($request->description ?? '') . 
                         ($request->item_dapur ? " (Alat: " . $request->item_dapur . ")" : "") . 
                         ($request->item_sergun ? " (Area: " . $request->item_sergun . ")" : ""),
                ];

                // Logic Sesi Waktu (Sama untuk semua mesin dalam satu booking)
                if ($request->slot_id) {
                    $slot = \App\Models\TimeSlot::find($request->slot_id);
                    $data['slot_id'] = $slot->id;
                    $data['start_time'] = $slot->start_time;
                    $data['end_time'] = $slot->end_time;
                } else {
                    $data['slot_id'] = null;
                    $data['start_time'] = $request->start_time;
                    $data['end_time'] = $request->end_time;
                }

                // 3. Cek Tabrakan PER MESIN/ALAT
                $cekTabrakan = \App\Models\Booking::where('facility_id', $fId)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status_id', [1, 2, 4])
                    ->where(function ($query) use ($data) {
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
        $role = $user->role->role_name;
        $userGender = $user->residentDetails->gender ?? null;
        $adminCategory = $user->assigned_category;

        // 1. Normalisasi kategori untuk pengecekan (mesin-cuci -> mesin_cuci)
        $normalizedCategory = str_replace('-', '_', strtolower($category));
        // 2. Proteksi Akses Admin: Jangan sampai Admin Dapur bisa buka URL Mesin Cuci
        // if ($role === 'Admin' && $normalizedCategory !== strtolower($adminCategory)) {
        //     return redirect()->route('booking.schedule', ['category' => str_replace('_', '-', $adminCategory)])
        //     ->with('error', 'Akses ditolak! Anda hanya admin ' . $adminCategory);
        //     }

        $search = $request->get('search');
        $itemFilter = $request->get('item');
        $title = ucwords(str_replace(['-', '_'], ' ', $category));

        // Ambil data booking untuk fasilitas terkait
        $query = \App\Models\Booking::with(['user.residentDetails', 'facility', 'status', 'slot']);
        // if ($user->role->role_name === 'Resident') {
        // $query->where('user_id', $user->id); 
        // }
        // Mapping Filter agar Schedule menampilkan data yang benar
        // 3. Filter Query Utama
        $query->whereHas('facility', function ($q) use ($normalizedCategory, $userGender, $role) {

            // Logika KHUSUS Mesin Cuci
            if (str_contains($normalizedCategory, 'mesin')) {
                if ($role === 'Manager') {
                    $q->where('name', 'LIKE', "%Mesin Cuci%");
                } else {
                    // KUNCI GENDER: Hanya tarik yang mengandung "Mesin Cuci [Gender]"
                    // Gunakan $userGender yang didapat dari database (Male/Female)
                    $q->where('name', 'LIKE', "Mesin Cuci $userGender%");
                }
            }
            // Logika Fasilitas Lainnya
            elseif ($normalizedCategory == 'cws') {
                $q->where('name', 'LIKE', "%Co-Working%");
            } elseif ($normalizedCategory == 'theater') {
                $q->where('name', 'LIKE', "%Theater%");
            } elseif ($normalizedCategory == 'sergun') {
                $q->where('name', 'LIKE', "%Serba Guna%");
            } else {
                // Dapur atau kategori lain: filter berdasarkan nama kategori
                $q->where('name', 'LIKE', "%" . str_replace('_', ' ', $normalizedCategory) . "%");
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
            $query->whereHas('user.residentDetails', function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%$search%");
            });
        }

        // dd($role, $userGender, $query->toSql(), $query->getBindings());
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

    public function adminAction(Booking $booking, $action)
    {
        $admin = Auth::user();
        $facilityName = strtolower($booking->facility->name);
        $adminCategory = strtolower($admin->assigned_category);
        $adminGender = strtolower($admin->residentDetails->gender ?? ''); // male or female

        // Pemetaan Alias (Singkatan ke Nama Lengkap)
        $aliasMap = [
            'dapur' => ['dapur', 'kitchen'],
            'cws' => ['co-working', 'cws', 'working space'],
            'sergun' => ['serba guna', 'sergun', 'hall'],
            'mesin_cuci' => ['mesin cuci', 'laundry'],
            'theater' => ['theater', 'theatre']
        ];

        // Cek Otoritas
        $hasAccess = false;
        if ($admin->role->role_name === 'Manager') {
            $hasAccess = true;
        } else {
            // Ambil daftar kata kunci berdasarkan kategori admin
            $keywords = $aliasMap[$adminCategory] ?? [$adminCategory];
            foreach ($keywords as $keyword) {
                if (str_contains($facilityName, $keyword)) {
                    $hasAccess = true;

                    // --- TAMBAHKAN PROTEKSI GENDER DI SINI ---
                    if ($adminCategory === 'mesin_cuci') {
                        // Cek apakah gender admin ada di dalam nama fasilitas
                        // Misal: Admin Male mau ACC "Mesin Cuci Female 1" -> Akan ditolak
                        if (!str_contains($facilityName, $adminGender)) {
                            $hasAccess = false;
                        }
                        break;
                    }
                }
            }

            if (!$hasAccess) {
                $msg = ($adminCategory === 'mesin_cuci') ? "Anda tidak berhak mengelola Mesin Cuci Gender Lain" : "Anda tidak berhak mengelola fasilitas ini";
                return back()->with('error', $msg);
            }

            // Eksekusi Perubahan Status
            if ($action === 'accept') {
                $booking->update(['status_id' => 2]); // ID 2: Accepted
                return back()->with('success', 'Peminjaman sudah di setujui.');
            } elseif ($action === 'cancel') {
                $booking->update(['status_id' => 3]); // ID 3: Canceled
                return back()->with('error', 'Peminjaman sudah tidak di setujui.');
            } elseif ($action === 'complete') {
                $booking->update([
                    'status_id' => 5,
                    'cleanliness_status' => 'approved',
                ]); // ID 5: Completed
                return back()->with('success', 'Foto kebersihan disetujui, peminjaman selesai!.');
            } else {
                return back()->with('error', 'Aksi tidak dikenali.');
            }

            return back();
        }
    }

    // Fungsi untuk Early Release (Penghuni)
    public function earlyRelease(Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);

        $booking->update([
            'is_early_release' => true,
            'actual_finish_at' => now(),
        ]);

        return back()->with('success', 'Peminjaman diakhiri lebih awal. Silakan upload foto kebersihan.');
    }
}
