<?php

namespace App\Http\Controllers;

use App\Models\BuildingComplaint; // TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    // app/Http/Controllers/ComplaintController.php

    public function index()
    {
    $user = Auth::user();
    
    // Jika dia Penghuni, filter berdasarkan nomor kamarnya
    if($user->role->role_name === 'Resident') {
        $userRoom = $user->residentDetails->room_number;
        
        $complaints = BuildingComplaint::whereHas('resident.residentDetails', function($q) use ($userRoom) {
            $q->where('room_number', $userRoom);
        })->latest()->paginate(10);
    } else {
        // Admin/Pengelola bisa lihat semua
        $complaints = BuildingComplaint::latest()->paginate(10);
    }

    return view('admin.complaint', compact('complaints'));
    }

    public function show($id)
    {
        $complaint = \App\Models\Complaint::with(['user.residentDetails', 'category', 'status'])
            ->findOrFail($id);

        // Keamanan: Jika bukan pemiliknya, dilarang akses (403 Forbidden)
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }

        return view('penghuni.complaintDetail', compact('complaint'));
    }

    public function create()
    {
        // Menampilkan view form tambah keluhan
        // Pastikan file view ini ada di: resources/views/admin/complaint/create.blade.php
        return view('admin.addComplaint');
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'location_item' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Handle Upload Foto jika ada
        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('complaints/building', 'public');
        }

        // 3. Simpan ke Database
        // Asumsi status_id = 1 adalah 'Submitted' atau 'Pending'
        \App\Models\BuildingComplaint::create([
            'resident_id' => Auth::id(),
            'location_item' => $request->location_item,
            'description' => $request->description,
            'photo_path' => $path,
            'status_id' => 1,
        ]);

        return redirect()->route('admin.complaint')->with('success', 'Keluhan berhasil dikirim dan akan segera diproses.');
    }

    // app/Http/Controllers/ComplaintController.php

    // app/Http/Controllers/ComplaintController.php

    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = BuildingComplaint::with(['resident.residentDetails', 'status']);

        // Jika Resident, filter berdasarkan kolom yang ada di database (misal: resident_id)
        if ($user->role->role_name === 'Resident') {
            // GANTI 'user_id' menjadi 'resident_id' jika itu nama kolom di DB kamu
            $query->where('resident_id', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('location_item', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        $complaints = $query->latest()->paginate(10);

        return view('admin.complaint', compact('complaints'));
    }
}