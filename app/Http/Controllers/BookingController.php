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

        return view('penghuni.addBooking', compact('facilities', 'user', 'kategori', 'myBookings', 'timeSlots', 'items'));
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
            return redirect()->route('booking.myBookings')->with('success', 'Booking berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

public function showSchedule(Request $request, $category)
    {
        $user = Auth::user();
        $role = $user->role->role_name;
        $userGender = $user->residentDetails->gender ?? null;

        $normalizedCategory = str_replace('-', '_', strtolower($category));
        $search = $request->get('search');
        $itemFilter = $request->get('item');
        $title = ucwords(str_replace(['-', '_'], ' ', $category));
        
        // 1. Eager Load semua yang dibutuhkan
        $query = \App\Models\Booking::with(['user.residentDetails', 'facility', 'facilityItem', 'status', 'slot']);

        // 2. Filter Kategori (Dibuat lebih fleksibel)
        $query->whereHas('facility', function ($q) use ($normalizedCategory, $userGender, $role) {
            if (str_contains($normalizedCategory, 'mesin')) {
                if ($role === 'Manager') {
                    $q->where('name', 'LIKE', "%Mesin Cuci%");
                } else {
                    $q->where('name', 'LIKE', "Mesin Cuci $userGender%");
                }
            } 
            // --- PERBAIKAN DI SINI ---
            elseif ($normalizedCategory == 'cws') {
                // Cari yang namanya CWS ATAU Co-Working ATAU Co Working
                $q->where(function($sub) {
                    $sub->where('name', 'LIKE', '%CWS%')
                        ->orWhere('name', 'LIKE', '%Co-Working%')
                        ->orWhere('name', 'LIKE', '%Co Working%');
                });
            } 
            elseif ($normalizedCategory == 'theater') {
                $q->where('name', 'LIKE', "%Theater%")->orWhere('name', 'LIKE', "%Theatre%");
            } 
            elseif ($normalizedCategory == 'sergun') {
                $q->where('name', 'LIKE', "%Serba Guna%")->orWhere('name', 'LIKE', "%Sergun%");
            } 
            else {
                $q->where('name', 'LIKE', "%" . str_replace('_', ' ', $normalizedCategory) . "%");
            }
        });

        // 3. Filter Berdasarkan Item (Alat/Area) - PENTING: Gunakan facility_item_id
        if ($itemFilter) {
            $query->where('facility_item_id', $itemFilter);
        }

        if ($search) {
            $query->whereHas('user.residentDetails', function ($q) use ($search) {
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
