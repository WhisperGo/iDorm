<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\BuildingComplaint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * Tampilan List Keluhan
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->role_name === 'Resident') {
            $userRoom = $user->residentDetails->room_number;

            // Logika Sekamar: Bisa lihat keluhan dari kamar yang sama
            $complaints = BuildingComplaint::whereHas('resident.residentDetails', function ($q) use ($userRoom) {
                $q->where('room_number', $userRoom);
            })->latest()->paginate(10);

            // DIUBAH: Arahkan ke view khusus penghuni
            return view('admin.complaint', compact('complaints'));
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

        // Admin/Pengelola bisa lihat semua
        $complaints = BuildingComplaint::latest()->paginate(10);
        return view('admin.complaint', compact('complaints'));
    }

    /**
     * Detail Keluhan untuk Penghuni
     */
    public function showResident($id)
    {
        // Gunakan BuildingComplaint agar konsisten dengan store()
        $complaint = BuildingComplaint::with(['resident.residentDetails', 'status'])
                                        ->findOrFail($id);

        $user = Auth::user();
        
        // Keamanan: Cek apakah keluhan berasal dari kamar yang sama
        if ($complaint->resident->residentDetails->room_number !== $user->residentDetails->room_number) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }
    }
    
    public function show($id)
    {
        $complaint = BuildingComplaint::with(['resident.residentDetails', 'status'])
                                                ->findOrFail($id);

        return view('admin.complaintDetail', compact('complaint'));
    }

    /**
     * Form Tambah Keluhan
     */
    public function create()
    {
        // Pastikan path view-nya sesuai dengan folder penghuni jika ada
        return view('admin.addComplaint');
    }

    /**
     * Simpan Keluhan Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_item' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('complaints/building', 'public');
        }

        BuildingComplaint::create([
            'resident_id' => Auth::id(),
            'location_item' => $request->location_item,
            'description' => $request->description,
            'photo_path' => $path,
            'status_id' => 1, // Default: Submitted
        ]);

        // DIUBAH: Redirect ke index milik penghuni
        return redirect()->route('complaint.index')->with('success', 'Keluhan berhasil dikirim!');
        return redirect()->route('admin.complaint')
                        ->with('success', 'Keluhan berhasil dikirim dan akan segera diproses.');
    }

    /**
     * Tampilan List Keluhan khusus Admin (dengan Fitur Search)
     */
    public function adminIndex(Request $request)
    {
        $search = $request->search;

        $complaints = BuildingComplaint::with(['resident.residentDetails', 'status'])
            ->when($search, function ($query, $search) {
                return $query->where('location_item', 'like', "%{$search}%")
                    ->orWhereHas('resident.residentDetails', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        return view('admin.complaint', compact('complaints'));
    }

    /**
     * Update Status Keluhan (Oleh Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $complaint = BuildingComplaint::findOrFail($id);

        $complaint->update([
            'status_id' => $request->status_id
        ]);

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }
}