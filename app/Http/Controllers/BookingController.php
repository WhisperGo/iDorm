<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Facility;
use App\Models\FacilityItem;
use App\Models\Suspension;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $kategori = $request->get('kategori_fasilitas');
        $gender = trim($user->residentDetails->gender ?? '');

        // 1. Inisialisasi variabel default agar compact tidak error
        $globalSuspend = null;
        $localSuspend = null;
        $parentFacility = null;
        $items = collect();
        $facilities = collect();
        $timeSlots = collect();

        // 2. Ambil riwayat booking user (selalu muncul di bawah form)
        $myBookings = Booking::where('user_id', $user->id)
            ->with(['facility', 'facilityItem', 'status', 'slot'])
            ->latest()
            ->get();

        // 3. CEK SUSPEND GLOBAL (Diberikan oleh Manager/Pengelola)
        $globalSuspend = \App\Models\Suspension::where('user_id', $user->id)
            ->whereNull('facility_id')
            ->active()
            ->first();

        // 4. LOGIKA JIKA TIDAK KENA GLOBAL SUSPEND
        if (!$globalSuspend && $kategori) {
            $cat = strtolower($kategori);

            // Mapping kategori ke nama fasilitas di DB
        $map = [
            'mesin_cuci' => 'Mesin Cuci',
            'dapur' => 'Dapur',
            'sergun' => 'Serba',
            'cws' => 'Co-Working',
            'theater' => 'Theater'
        ];

        if (isset($map[$cat])) {
            $parentFacility = Facility::where('name', 'LIKE', '%' . $map[$cat] . '%')->first();
        }

        // A. CEK SUSPEND LOKAL (Spesifik Fasilitas ini)
        if ($parentFacility) {
            $localSuspend = $this->checkSuspension($user->id, $parentFacility->id);
              // B. AMBIL ITEM & TIMESLOT (Hanya jika tidak kena suspend lokal)

                if (!$localSuspend) {
                    $facilities = collect([$parentFacility]);
                    $allItems = FacilityItem::where('facility_id', $parentFacility->id)->get();
                     // Filter Gender khusus Mesin Cuci

                    if ($cat == 'mesin_cuci' && !empty($gender)) {
                        $cleanGender = strtolower($gender);
                        $items = $allItems->filter(function ($item) use ($cleanGender) {
                            $itemName = strtolower($item->name);
                            if ($cleanGender === 'male') {
                                return str_contains($itemName, 'male') && !str_contains($itemName, 'female');
                            }
                            return str_contains($itemName, $cleanGender);
                        });
                        } else {
                            $items = $allItems;
                        }
                        // Ambil TimeSlots (Sesuaikan kategori timeslot anda)
                        $timeSlots = TimeSlot::where('facilities', 'heavy')->get();

                }
            }
        }
        // 5. Kirim semua data ke View

        return view('feature.bookings.add_booking', compact(
            'facilities',
            'items',
            'timeSlots',
            'kategori',
            'user',
            'myBookings',
            'globalSuspend',
            'localSuspend'
            ));
        }
        
    public function store(Request $request){
        $user = Auth::user();
        if ($request->has('facility_id')) {
        $suspend = $this->checkSuspension($user->id, $request->facility_id);

            if ($suspend) {
            return back()->with('error', 'Gagal: Anda sedang dalam masa hukuman (Suspend).');

            }
        }
        $kategori = $request->kategori;
        $bookingDate = $request->booking_date;

         // === 1. VALIDASI INPUT DASAR ===

        $rules = [
            'facility_id' => 'required|exists:facilities,id',
            'kategori' => 'required|in:dapur,cws,mesin_cuci,theater,sergun',
            'booking_date' => 'required|date|after_or_equal:today',
        ];
        if ($kategori == 'mesin_cuci') {
        $rules['facility_item_id'] = 'required|array|min:1|max:2'; // Max 2 mesin

            $rules['facility_item_id.*'] = 'exists:facility_items,id';
            $rules['slot_id'] = 'required|exists:time_slots,id';
        } else {
            $rules['facility_item_id'] = 'required|exists:facility_items,id';
            $rules['start_time'] = 'required|date_format:H:i';
            $rules['end_time'] = 'required|date_format:H:i|after:start_time'; // Rule 4: End > Start
            if ($kategori == 'cws')
            $rules['jumlah_orang'] = 'required|numeric|min:20';

            if ($kategori == 'theater') {
                $rules['description'] = 'required|max:255';
                $rules['jumlah_orang'] = 'required|numeric|min:1|max:50';
            }
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'date' => ':attribute bukan tanggal yang valid.',
            'after_or_equal' => ':attribute tidak boleh di masa lalu.',
            'array' => ':attribute harus berupa array.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'date_format' => 'Format :attribute tidak valid.',
            'after' => ':attribute harus setelah waktu mulai.',
            'numeric' => ':attribute harus berupa angka.',
        ];

        $customAttributes = [
            'facility_id' => 'Fasilitas',
            'kategori' => 'Kategori',
            'booking_date' => 'Tanggal peminjaman',
            'facility_item_id' => 'Item fasilitas',
            'slot_id' => 'Sesi waktu',
            'start_time' => 'Jam mulai',
            'end_time' => 'Jam selesai',
            'jumlah_orang' => 'Jumlah orang',
            'description' => 'Deskripsi / judul',
        ];

        $request->validate($rules, $messages, $customAttributes);
        

         // === 2. PERSIAPAN DATA WAKTU ===
        $startTime = null;

        $endTime = null;
        $slotId = null;
        $itemsToCheck = [];
        if ($kategori == 'mesin_cuci') {
        $slot = TimeSlot::findOrFail($request->slot_id);

            $slotId = $slot->id;
            $startTime = $slot->start_time;
            $endTime = $slot->end_time;
            $itemsToCheck = $request->facility_item_id; // Array ID
        } else {
            $startTime = $request->start_time;
            $endTime = $request->end_time;
            $itemsToCheck = [$request->facility_item_id]; // Bungkus jadi array
        }
            // Konversi ke Carbon untuk validasi tanggal/jam yang lebih mudah
            $carbonDate = Carbon::parse($bookingDate);

        $carbonNow = Carbon::now();
        $bookingStartDateTime = Carbon::parse("$bookingDate $startTime");
        $bookingEndDateTime = Carbon::parse("$bookingDate $endTime");
        // === 3. VALIDASI ATURAN BISNIS KHUSUS ===
        
        // RULE 8: Masa pakai semua fasilitas adalah 2 jam (Kecuali mesin cuci yg sudah ikut slot)

        if ($kategori != 'mesin cuci') {
            $durationInMinutes = $bookingStartDateTime->diffInMinutes($bookingEndDateTime);
            // Toleransi +/- 1 menit jika perlu, tapi strict 120 menit (2 jam)
            if ($durationInMinutes > 120) {
                return back()->with('error', 'Durasi peminjaman tidak lebih dari 2 jam')->withInput();
            }
        }
        // RULE 5 & 6 & 7: Batas Waktu Booking (H-1 dsb)

        if ($kategori == 'dapur') {
            // Rule 6: Minimal H-1 Jam
            // Rule 7: Jam lewat tidak bisa booking
            if ($bookingStartDateTime->isPast()) {
                return back()->with('error', 'Waktu booking sudah lewat.')->withInput();
            }
            if ($bookingStartDateTime->diffInHours($carbonNow) < 1 && $bookingStartDateTime->isFuture()) {
                // Logic H-1 jam: Waktu booking harus minimal 1 jam dari sekarang
                // Opsional: sesuaikan jika H-1 jam maksudnya cutoff time
                }
            } else {
                // Rule 5: H-1 (Besok baru bisa booking, kecuali hari ini masih H-1 nya besok? 
                // Interpretasi: "H-1 artinya 1 hari sebelumnya masih bisa booking" -> Artinya booking minimal untuk besok?
                // "walaupun saya booking pukul 23.59 hari ini, saya masih bisa booking fasilitas untuk pukul 07.00 hari besok"
                // Artinya: Booking untuk HARI INI tidak boleh untuk kategori selain dapur.
                if ($carbonDate->isToday()) {
                return back()->with('error', 'Fasilitas ini harus dibooking minimal H-1 (1 hari sebelumnya).')->withInput();

            }
        }
        // Rule CWS Kamis Libur

        if ($kategori == 'cws' && $carbonDate->isThursday()) {
            return back()->with('error', 'Mohon maaf, Co-Working Space tutup setiap hari Kamis.')->withInput();
        }

         // === 4. VALIDASI KONFLIK DATABASE (Conflict Check) ===
        
        try {
        DB::beginTransaction();

             // RULE 1: Mesin Cuci Max 2 & Cek Quota User di Jam Sama

            if ($kategori == 'mesin_cuci') {
                // Cek apakah user SUDAH punya booking mesin cuci LAIN di slot tanggal & jam yang SAMA?
                $existingUserBookings = Booking::where('user_id', $user->id)
                    ->where('booking_date', $bookingDate)
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->whereHas('facility', function ($q) {
                            $q->where('name', 'LIKE', '%Mesin Cuci%');
                        })
                        ->whereIn('status_id', [1, 2, 4]) // Aktif
                        ->count();
                    // $itemsToCheck adalah array yang mau dibooking sekarang (misal pilih 2)

                 // Total mesin = yg sudah dibooking + yg mau dibooking
                if (($existingUserBookings + count($itemsToCheck)) > 2) {
                    DB::rollBack();
                    return back()->with('error', 'Anda hanya boleh menggunakan maksimal 2 mesin cuci di sesi waktu yang sama.')->withInput();
                }
            }

             // RULE 2 & 3: Cek Tabrakan Jadwal (Room/Item Availability)

            foreach ($itemsToCheck as $itemId) {
                // Cek apakah Item ini sudah dibooking orang lain di jam segitu?
                $isConflict = Booking::where('facility_item_id', $itemId)
                    ->where('booking_date', $bookingDate)
                    ->whereIn('status_id', [1, 2, 4]) // Booked/Approved/Active
                    ->where(function ($query) use ($startTime, $endTime) {
                        // Logic Overlap: (Start A < End B) AND (End A > Start B)
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                        })
                        ->exists();
                    if ($isConflict) {

                    $itemName = FacilityItem::find($itemId)->name ?? 'Item';
                    DB::rollBack();
                    return back()->with('error', "Gagal! $itemName sudah dibooking orang lain pada jam tersebut.")->withInput();
                }
                // SIMPAN

                Booking::create([
                    'user_id' => $user->id,
                    'facility_id' => $request->facility_id,
                    'facility_item_id' => $itemId,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_id' => $slotId,
                    'status_id' => 1, // Pending
                        'cleanliness_status' => 'pending',
                        'description' => $request->description,
                        'jumlah_orang' => $request->jumlah_orang,
                    ]);
                }
                DB::commit();
                return redirect()->route('booking.my_bookings')->with('success', 'Booking berhasil dibuat!');

        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error("Booking Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
    public function showSchedule(Request $request, $category)

    {
        $user = Auth::user();
        $title = ucwords(str_replace(['-', '_'], ' ', $category));
        $role = $user->role->role_name;
        $rawGender = $user->residentDetails->gender ?? $user->adminDetails->gender ?? null;
        $userGender = $rawGender ? strtolower(trim($rawGender)) : null;
        $normalizedCategory = str_replace('-', '_', strtolower($category));
        $itemFilter = $request->get('item');
        $query = Booking::with(['user.residentDetails', 'user.adminDetails', 'facility', 'facilityItem', 'status']);
            $totalRaw = Booking::where('facility_id', 2)->count(); // Total semua (non-deleted)
            $totalFiltered = $query->count(); // Total setelah filter gender
            // 2. Tentukan ID Fasilitas secara Hardcode berdasarkan kategori (Lebih Aman)
            // Sesuai dd kamu: 1=Dapur, 2=Mesin Cuci, 3=Theater, 4=CWS, 5=Sergun

        $facilityId = match ($normalizedCategory) {
            'dapur' => 1,
            'mesin_cuci', 'laundry' => 2,
            'theater' => 3,
            'cws' => 4,
            'sergun' => 5,
            default => null
        };
        // Inisialisasi Query langsung kunci di Facility ID
            $query = Booking::query()

             ->where('facility_id', $facilityId) // KUNCI UTAMA: Biar gak bocor ke fasilitas lain
            ->with(['user.residentDetails', 'user.adminDetails', 'facility', 'facilityItem', 'status']);
        // 3. FILTER GENDER (Hanya untuk Mesin Cuci & Bukan Manager)

        if ($role !== 'Manager' && $facilityId == 2) {
            if ($userGender) {
                $query->whereHas('facilityItem', function ($q) use ($userGender) {
                    // Pakai '=' atau 'LIKE' yang sangat spesifik
                    // Pastikan item mengandung kata Male saja atau Female saja
                    $q->where('name', 'LIKE', "%$userGender%");
                });
                // Tambahan: Pastikan user yang booking juga gendernya sama (Double Lock)

                $query->whereHas('user.residentDetails', function ($q) use ($userGender) {
                    $q->where('gender', $userGender);
                });
            } else {
                $query->whereRaw('1 = 0'); // Jika gender tidak jelas, kosongkan
            }
        }
        // 4. Filter Dropdown Item

        if ($itemFilter) {
            $query->where('facility_item_id', $itemFilter);
        }
        $bookings = $query->latest()->paginate(10);
        
        return view('feature.bookings.booking_schedule', compact('bookings', 'category', 'title'));
        }

    public function updateCleanliness(Request $request, $id)

    {
        $booking = Booking::findOrFail($id);
        $action = $request->input('action');
        // Kita cari ID status berdasarkan nama (agar aman jika ID berubah, tapi logikanya tetap sama)
        // Atau bisa hardcode: Completed = 6, Awaiting = 7

         $completedStatusId = BookingStatus::where('status_name', 'Completed')->value('id'); // ID 6
        $awaitingStatusId = BookingStatus::where('status_name', 'Awaiting Cleanliness Photo')->value('id'); // ID 7
        if ($action === 'approved') {
        // SKENARIO APPROVE:

             // Ubah status dari 'Verifying Cleanliness' (5) menjadi 'Completed' (6)
            $booking->update([
                'status_id' => $completedStatusId,
            ]);
            return redirect()->back()->with('success', 'Bukti diterima. Status menjadi Completed.');
            
        } elseif ($action === 'rejected') {

             // SKENARIO REJECT:
            // 1. Hapus file lama (opsional, biar bersih storage-nya)
            if ($booking->photo_proof_path && \Storage::disk('public')->exists($booking->photo_proof_path)) {
                \Storage::disk('public')->delete($booking->photo_proof_path);
            }
            // 2. Ubah status dari 'Verifying Cleanliness' (5) BALIK ke 'Awaiting Cleanliness Photo' (7)
            // 3. Null-kan path foto agar di halaman user (my_bookingi) form upload muncul lagi.

            $booking->update([
                'status_id' => $awaitingStatusId,
                'photo_proof_path' => null
            ]);
            return redirect()->back()->with('success', 'Bukti ditolak. User diminta upload ulang.');
            }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
        }

     // Fungsi bantu biar kodingan rapi
    private function applyGenderFilter($query, $gender)
    {
        $query->whereHas('facilityItem', function ($q) use ($gender) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$gender}%"]);
        });
    }
    // app/Http/Controllers/BookingController.php
    
    public function uploadPhoto(Request $request, Booking $booking)

    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('cleanliness', 'public');

             // Cari ID untuk status 'Verifying Cleanliness'
            // Jika kamu yakin ID-nya 5, bisa langsung hardcode 5.

             // Tapi lebih aman cari berdasarkan nama agar tidak error jika ID berubah.
            $verifyingStatus = BookingStatus::where('status_name', 'Verifying Cleanliness')->first();
            $statusId = $verifyingStatus ? $verifyingStatus->id : 5;
            
            $booking->update([
            'photo_proof_path' => $path,

                'cleanliness_status' => 'pending',
                'status_id' => $statusId,
                'is_early_release' => 0
            ]);
            return back()->with('success', 'Foto kebersihan berhasil diunggah.');
            }

    }
    public function myPersonalHistory()

    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
        ->with(['facility', 'facilityItem', 'status', 'slot'])

            ->latest('booking_date')
            ->latest('start_time')
            ->get();
        // Grouping tetap dipertahankan agar tampilan rapi jika user booking >1 item di jam yang sama

        $groupedBookings = $bookings->groupBy(function ($item) {
            return $item->booking_date . '_' . $item->start_time . '_' . $item->end_time . '_' . $item->facility_id;
        });
        // Nama file view disesuaikan dengan yang kamu sebutkan (my_bookingi)
        return view('feature.bookings.personal_bookings', compact('groupedBookings'));

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

            if ($action === 'approve') {
                $booking->update(['status_id' => 2]); // ID 2: Accepted
                return back()->with('success', 'Peminjaman sudah di setujui.');
            } elseif ($action === 'reject') {
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
        // Gunakan Auth::id() untuk membandingkan ID (angka), bukan objek user
        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        $now = now('Asia/Jakarta');
        
        // SINKRONISASI: Cari status yang namanya sesuai dengan logika di Blade
        // Di Blade kamu pakai 'Awaiting Cleanliness Photo', pastikan di DB namanya sama.

        $targetStatus = BookingStatus::where('status_name', 'Awaiting Cleanliness Photo')->first();
        $booking->update([
        'end_time' => $now->format('H:i:s'),

            'status_id' => $targetStatus->id,
            'is_early_release' => true,
        ]);
        return redirect()->back()->with('success', 'Sesi diakhiri lebih awal. Silakan upload foto kebersihan!');
        }

    private function checkSuspension($userId, $facilityId)

    {
        return Suspension::where('user_id', $userId)
            ->where(function ($query) use ($facilityId) {
                $query->where('facility_id', $facilityId)   // Cek suspend spesifik fasilitas
                    ->orWhereNull('facility_id');         // Cek suspend global (Pengelola)
            })
            ->active() // <--- INI DIA ACTIVE SCOPE-NYA (Pengganti logika tanggal yang ribet)
            ->first();
    }
    
    // --- B. METHOD CHATBOT (Khusus mapping data AI) ---
    public function createFromChatbot(Request $request)
    {
        $user = Auth::user();
    
        // 1. Mapping Ruangan AI ke Kategori Sistem
        $room = strtolower($request->query('room'));
        $kategori = match ($room) {
            'dapur' => 'dapur',
            'cws', 'co-working' => 'cws',
            'theater' => 'theater',
            'mesin cuci', 'laundry' => 'mesin_cuci',
            'sergun' => 'sergun',
            default => 'dapur' 
        };
    
        // 2. Mapping Variabel agar sesuai dengan nama di View/Form
        $bookingDate = $request->query('date'); 
        $startTime    = $request->query('start'); // Format 20.00
        $endTime      = $request->query('end');   // Format 22.00
    
        // 3. Inisialisasi variabel default agar compact tidak error
        $globalSuspend = null;
        $localSuspend = null;
        $parentFacility = null;
        $items = collect();
        $facilities = collect();
        $timeSlots = collect();
        $tools = collect(); // Untuk alat dapur jika ada

        // 4. Ambil riwayat booking user
        $myBookings = Booking::where('user_id', $user->id)
            ->with(['facility', 'facilityItem', 'status', 'slot'])
            ->latest()
            ->get();

        // 5. Cek Suspend Global
        $globalSuspend = \App\Models\Suspension::where('user_id', $user->id)
            ->whereNull('facility_id')
            ->active()
            ->first();

        // 6. Logika Pencarian Fasilitas (Sama dengan manual)
        if (!$globalSuspend && $kategori) {
            $cat = strtolower($kategori);
            $map = [
                'mesin_cuci' => 'Mesin Cuci',
                'dapur' => 'Dapur',
                'sergun' => 'Serba',
                'cws' => 'Co-Working',
                'theater' => 'Theater'
            ];

            if (isset($map[$cat])) {
                $parentFacility = Facility::where('name', 'LIKE', '%' . $map[$cat] . '%')->first();
            }

            if ($parentFacility) {
                $localSuspend = $this->checkSuspension($user->id, $parentFacility->id);

                if (!$localSuspend) {
                    $facilities = collect([$parentFacility]);
                    $allItems = FacilityItem::where('facility_id', $parentFacility->id)->get();

                    // Filter Gender khusus Mesin Cuci
                    if ($cat == 'mesin_cuci' && !empty($gender)) {
                        $cleanGender = strtolower($gender);
                        $items = $allItems->filter(function ($item) use ($cleanGender) {
                            $itemName = strtolower($item->name);
                            if ($cleanGender === 'male') {
                                return str_contains($itemName, 'male') && !str_contains($itemName, 'female');
                            }
                            return str_contains($itemName, $cleanGender);
                        });
                    } else {
                        $items = $allItems;
                    }

                    // Khusus Dapur: Pisahkan alat masak ke variabel $tools
                    if ($cat == 'dapur') {
                        $tools = $allItems->filter(function($item) {
                            return str_contains(strtolower($item->name), 'alat') || 
                                   str_contains(strtolower($item->name), 'panci');
                        });
                    }

                    $timeSlots = TimeSlot::where('facilities', 'heavy')->get();
                }
            }
        }

        // 7. Kirim data ke view
        return view('feature.bookings.add_booking', compact(
            'kategori', 'bookingDate', 'startTime', 'endTime', 'user',
            'facilities', 'items', 'timeSlots', 'tools', 'myBookings', 'globalSuspend', 'localSuspend'
        ));
    }
}
