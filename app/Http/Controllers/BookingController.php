<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityItem;
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
        $kategori = $request->get('kategori_fasilitas');
        $user = Auth::user();
        $gender = trim($user->residentDetails->gender ?? '');

        $facilities = collect();
        $items = collect();

        $parentFacility = Facility::query()
            ->when($kategori, function ($query) use ($kategori) {
                $cat = strtolower($kategori);
                if ($cat == 'mesin_cuci') return $query->where('name', 'LIKE', '%Mesin Cuci%');
                if ($cat == 'dapur') return $query->where('name', 'LIKE', '%Dapur%');
                if ($cat == 'sergun') return $query->where('name', 'LIKE', '%Serba%');
                if ($cat == 'cws') return $query->where('name', 'LIKE', '%Co-Working%');
                if ($cat == 'theater') return $query->where('name', 'LIKE', '%Theater%');
                return $query->where('id', 0);
            })->first();

        if ($parentFacility) {
            $facilities = collect([$parentFacility]);
            $allItems = \App\Models\FacilityItem::where('facility_id', $parentFacility->id)->get();

            if (strtolower($kategori) == 'mesin_cuci' && !empty($gender)) {
                $cleanGender = strtolower($gender);
                $items = $allItems->filter(function($item) use ($cleanGender) {
                    $itemName = strtolower($item->name);
                    if ($cleanGender === 'male') {
                        return str_contains($itemName, 'male') && !str_contains($itemName, 'female');
                    }
                    return str_contains($itemName, $cleanGender);
                });
            } else {
                $items = $allItems;
            }
        }

        $timeSlots = TimeSlot::where('facilities', 'heavy')->get();
        $myBookings = Booking::where('user_id', $user->id)->with(['facility', 'status', 'slot'])->latest()->get();

        return view('penghuni.add_booking', compact('facilities', 'user', 'kategori', 'myBookings', 'timeSlots', 'items'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $rules = [
            'facility_id' => 'required|exists:facilities,id',
            'facility_item_id' => 'required',
            'booking_date' => 'required|date|after_or_equal:today',
            'kategori' => 'required',
            // 'facility_id.*' => 'exists:facilities,id',
        ];

        if ($request->kategori == 'cws') {
            $rules['jumlah_orang'] = 'required|numeric|min:20';
        } elseif ($request->kategori == 'theater') {
            $rules['description'] = 'required|max:255';
            $rules['jumlah_orang'] = 'required|numeric|min:1|max:50';
        }

        if ($request->has('slot_id') && $request->slot_id != null) {
            $rules['slot_id'] = 'required|exists:time_slots,id';
        } else {
            $rules['start_time'] = 'required';
            $rules['end_time'] = 'required|after:start_time';
        }

        $validated = $request->validate($rules);

        if ($request->kategori == 'cws') {
            $date = \Carbon\Carbon::parse($request->booking_date);

            if ($request->kategori == 'cws' && Carbon::parse($request->booking_date)->isThursday()) {
                return redirect()->back()->with('error', 'CWS tutup setiap hari Kamis.')->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // 1. Ambil semua ID fasilitas (bisa satu ID atau array ID mesin cuci)
            $itemIds = is_array($request->facility_item_id) ? $request->facility_item_id : [$request->facility_item_id];

            if (empty($facilityIds) || !$facilityIds[0]) {
                return redirect()->back()->with('error', 'Silakan pilih fasilitas/mesin!');
            }

            foreach ($itemIds as $itemId) {
                $data = [
                    'user_id' => Auth::id(),
                    'facility_id' => $request->facility_id,
                    'facility_item_id' => $itemId,
                    'booking_date' => $request->booking_date,
                    'status_id' => 1,
                    'cleanliness_status' => 'pending',
                    'description' => $request->description,
                    'jumlah_orang' => $request->jumlah_orang
                ];

                if ($request->slot_id) {
                    $slot = TimeSlot::find($request->slot_id);
                    $data['slot_id'] = $slot->id;
                    $data['start_time'] = $slot->start_time;
                    $data['end_time'] = $slot->end_time;
                } else {
                    $data['start_time'] = $request->start_time;
                    $data['end_time'] = $request->end_time;
                }

                // 3. Cek Tabrakan berdasarkan ITEM SPESIFIK
                $cekTabrakan = Booking::where('facility_item_id', $itemId)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status_id', [1, 2, 4])
                    ->where(function ($query) use ($data) {
                        $query->where('start_time', '<', $data['end_time'])
                                ->where('end_time', '>', $data['start_time']);
                    })->exists();

                if ($cekTabrakan) {
                    $namaItem = FacilityItem::find($itemId)->name;
                    DB::rollBack();
                    return redirect()->back()->with('error', "Waduh! $namaItem sudah dibooking pada jam tersebut.")->withInput();
                }

                Booking::create($data);
            }

            DB::commit();
            return redirect()->route('booking.my_bookings')->with('success', 'Booking berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
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
        $facilityId = match($normalizedCategory) {
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
                $query->whereHas('facilityItem', function($q) use ($userGender) {
                    // Pakai '=' atau 'LIKE' yang sangat spesifik
                    // Pastikan item mengandung kata Male saja atau Female saja
                    $q->where('name', 'LIKE', "%$userGender%");
                });

                // Tambahan: Pastikan user yang booking juga gendernya sama (Double Lock)
                $query->whereHas('user.residentDetails', function($q) use ($userGender) {
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

        return view('view_schedule', compact('bookings', 'category', 'title'));
    }

    // Fungsi bantu biar kodingan rapi
    private function applyGenderFilter($query, $gender) {
        $query->whereHas('facilityItem', function($q) use ($gender) {
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

        return view('penghuni.my_bookings', compact('myBookings'));
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
