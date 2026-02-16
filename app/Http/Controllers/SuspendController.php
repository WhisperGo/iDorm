<?php

namespace App\Http\Controllers;

use App\Models\Suspension;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuspendController extends Controller
{
    /**
     * Menampilkan daftar orang yang sedang dihukum (Active Suspensions)
     */
    public function index()
    {
        // Ambil data suspend yang sedang aktif saja (pakai Scope Active yang kita buat)
        // Eager load relasi user, facility, dan issuer biar hemat query
        $suspensions = Suspension::active()
            ->with(['user.residentDetails', 'facility', 'issuer'])
            ->latest()
            ->paginate(10);

        // Ambil daftar resident untuk dropdown pilihan
        $residents = User::whereHas('role', function($q) {
            $q->where('role_name', 'Resident');
        })->get();

        // Ambil fasilitas (Jika Manager, ambil semua. Jika Admin, ambil sesuai haknya)
        // Disini saya ambil semua dulu, logic filter ada di View/Blade
        $facilities = Facility::all();

        return view('feature.suspensions.index', compact('suspensions', 'residents', 'facilities'));
    }

    /**
     * EKSEKUSI HUKUMAN (Store)
     */
    public function store(Request $request)
    {
        $admin = Auth::user();
        $roleName = $admin->role->role_name ?? '';

        // 1. Validasi Input
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'reason'      => 'required|string|min:5|max:255',
            // facility_id BOLEH NULL (Global Suspend)
            'facility_id' => 'nullable|exists:facilities,id',
        ]);

        // 2. Logic Otoritas (PENTING!)
        $facilityId = $request->facility_id;

        // Jika dia BUKAN Manager, dia TIDAK BOLEH melakukan Global Suspend (facility_id = null)
        if ($roleName !== 'Manager' && is_null($facilityId)) {
            return back()->with('error', 'Hanya Manager yang memiliki otoritas untuk memblokir akses ke SEMUA fasilitas.');
        }

        // 3. Simpan ke Database
        Suspension::create([
            'user_id'     => $request->user_id,
            'facility_id' => $facilityId, // NULL = Global, Angka = Spesifik
            'issued_by'   => $admin->id,
            'reason'      => $request->reason,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        return back()->with('success', 'Hukuman suspend berhasil dijatuhkan.');
    }

    /**
     * Cabut Hukuman (Soft Delete)
     */
    public function destroy($id)
    {
        $suspension = Suspension::findOrFail($id);
        
        // Opsional: Cek apakah admin yang mau menghapus punya hak?
        // Misal: Admin Dapur gak boleh hapus hukuman yang dibuat Manager.
        
        $suspension->delete(); // Ini akan soft delete (masih ada di DB tapi dianggap hilang)

        return back()->with('success', 'Hukuman berhasil dicabut.');
    }
}
