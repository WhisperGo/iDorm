<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BuildingComplaint; // TAMBAHKAN INI

class ComplaintController extends Controller
{
    // app/Http/Controllers/ComplaintController.php

    public function index()
    {
        $user = Auth::user();

        // Jika dia Penghuni, filter berdasarkan nomor kamarnya
        if ($user->role->role_name === 'Resident') {
            $userRoom = $user->residentDetails->room_number;

            $complaints = BuildingComplaint::whereHas(
                'resident.residentDetails',
                function ($q) use ($userRoom) {
                    $q->where('room_number', $userRoom);
            })
            ->latest()
            ->paginate(10);
        } else {
            //Pengelola bisa lihat semua
            $complaints = BuildingComplaint::with(['resident.residentDetails', 'status'])
                                            ->latest()
                                            ->paginate(10);
        }

        return view('admin.complaint', compact('complaints'));
    }

    public function show($id)
    {
        $complaint = BuildingComplaint::with(['resident.residentDetails', 'status'])
                                                ->findOrFail($id);

        return view('admin.complaintDetail', compact('complaint'));
    }

    public function showManager($id)
    {
        $complaint = \App\Models\BuildingComplaint::with(['resident.residentDetails', 'status'])
            ->findOrFail($id);

        return view('pengelola.complaintDetail', compact('complaint'));
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

        return redirect()->route('admin.complaint')
                        ->with('success', 'Keluhan berhasil dikirim dan akan segera diproses.');
    }

    // app/Http/Controllers/ComplaintController.php

    // app/Http/Controllers/ComplaintController.php

    public function adminIndex(Request $request)
    {
        $search = $request->search;

        // GUNAKAN BuildingComplaint, karena model ini yang punya 'location_item'
        $complaints = BuildingComplaint::with(['resident.residentDetails', 'status'])
            ->when($search, function ($query, $search) {
                return $query->where('location_item', 'like', "%{$search}%")
                    ->orWhereHas('resident.residentDetails', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        // Kirim variabel ke view
        return view('admin.complaint', compact('complaints'));
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = \App\Models\BuildingComplaint::findOrFail($id);

        // Validasi sederhana: pastikan status_id valid (asumsi 3 adalah 'Resolved')
        $complaint->update([
            'status_id' => $request->status_id
        ]);

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }
}