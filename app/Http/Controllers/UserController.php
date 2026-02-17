<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\ResidentDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Menampilkan Form Tambah Resident
    public function createResident()
    {
        return view('feature.create_resident'); 
    }
    
    // 2. Memproses Simpan Data Resident
    public function storeResident(Request $request)
    {
        // Validasi Input
        $request->validate([
            'card_id'      => 'required|string|size:4|unique:users,card_id',
            'full_name'    => 'required|string|max:255',
            'gender'       => 'required|in:Male,Female',
            'room_number'  => 'required|string',
            'class_name'   => 'required|string|max:100',
            'phone_number' => 'nullable|string',
            // 'password'     => 'required|min:8|confirmed',
        ]);
    
        // A. Simpan ke Tabel Users (Akun Login)
        $user = User::create([
            'card_id' => $request->card_id,
            'role_id' => 3, // Role ID 3 = Resident
            'password' => Hash::make('password'),
            'account_status' => 'active',
        ]);
    
        // B. Simpan ke Tabel ResidentDetails (Profil)
        $user->residentDetails()->create([
            'full_name'    => $request->full_name,
            'gender'       => $request->gender,
            'room_number'  => $request->room_number,
            'class_name'  => $request->class_name,
            'phone_number' => $request->phone_number,
        ]);
    
        // Kembali ke halaman daftar resident dengan pesan sukses
        return redirect()->route('pengelola.resident')->with('success', 'Resident ' . $request->full_name . ' berhasil ditambahkan!');
    }
    // List Semua User (Admin & Resident)
    public function index()
    {
        $residents = User::with(['residentDetails', 'activeSuspensions'])
            ->whereIn('role_id', [2, 3])
            ->latest()
            ->paginate(10);

        return view('pengelola.users.index', [
            'residents' => $residents,
            'isManager' => Auth::user()->role_id == 1,
            'isAdmin'   => Auth::user()->role_id == 2,
            'facilities' => Facility::all(),
            'myFacilityId' => Auth::user()->adminDetails?->facility_id,
            'myFacilityName' => Auth::user()->adminDetails?->facility?->name,
            'isLaundryAdmin' => Auth::user()->adminDetails?->facility?->name === 'Mesin Cuci',
            'adminGender' => Auth::user()->role_id == 2 ? Auth::user()->adminDetails?->gender : Auth::user()->residentDetails?->gender,
        ]);
    }

    // Form Edit Profile
    public function edit($id)
    {
        // Eager load semua kemungkinan detail
        $user = User::with(['residentDetails', 'adminDetails.facility'])->findOrFail($id);
        $facilities = Facility::all(); // Dibutuhkan untuk dropdown di view edit
        
        return view('pengelola.users.edit', compact('user', 'facilities'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'gender'       => 'required|in:Male,Female',
            'phone_number' => 'nullable|string',
            'password'     => 'nullable|min:8|confirmed',
            'room_number'  => 'nullable|string',
            'facility_id'  => 'nullable|exists:facilities,id',
        ]);
    
        // 1. Update Password Jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
    
        // 2. LOGIKA UPDATE DETAIL (Berdasarkan Role)
        if ($user->role_id == 2) { 
            // JIKA ADMIN
            $adminData = [
                'full_name'    => $request->full_name,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
            ];

            // PENTING: Hanya update facility_id jika ada di request (mencegah error null saat input disabled)
            if ($request->has('facility_id')) {
                $adminData['facility_id'] = $request->facility_id;
            }

            $user->adminDetails()->updateOrCreate(
                ['user_id' => $user->id],
                $adminData
            );

        } else {
            // JIKA RESIDENT (Role 3)
            $user->residentDetails()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name'    => $request->full_name,
                    'gender'       => $request->gender,
                    'phone_number' => $request->phone_number,
                    'room_number'  => $request->room_number,
                ]
            );
        }
    
        // 3. Redirect sesuai role_id
        $routeName = ($user->role_id == 2) ? 'manager.admins.index' : 'pengelola.resident';
        return redirect()->route($routeName)->with('success', 'Data ' . $request->full_name . ' berhasil diperbarui!');
    }

    public function destroyResident($id)
    {
        $user = User::findOrFail($id);

        $user->delete();
        
        if ($user->residentDetails) {
            $user->residentDetails()->delete();
        }

        return redirect()->route('pengelola.resident')->with('success', 'Data resident berhasil dihapus secara permanen.');
    }

    // List Data Penghuni (Resident - role_id 3)
    public function residentIndex()
    {
        $residents = User::with(['residentDetails', 'activeSuspensions'])
            ->where('role_id', 3)
            ->latest()
            ->paginate(10);

        return view('feature.resident_management', [
            'residents' => $residents,
            'isManager' => Auth::user()->role_id == 1,
            'isAdmin'   => Auth::user()->role_id == 2,
            'facilities' => Facility::all(),
            'myFacilityId' => Auth::user()->adminDetails?->facility_id,
            'myFacilityName' => Auth::user()->adminDetails?->facility?->name,
            'isLaundryAdmin' => Auth::user()->adminDetails?->facility?->name === 'Mesin Cuci',
            'adminGender' => Auth::user()->role_id == 2 ? Auth::user()->adminDetails?->gender : Auth::user()->residentDetails?->gender,
        ]);
    }

    // List Data Admin Fasilitas (Admin - role_id 2)
    public function adminIndex()
    {
        $admins = User::with(['adminDetails.facility'])
            ->where('role_id', 2)
            ->latest()
            ->paginate(10);

        return view('feature.admin_management', [
            'admins' => $admins,
            'isManager' => Auth::user()->role_id == 1,
            'isAdmin'   => Auth::user()->role_id == 2,
            'title' => 'Facility Admin Management'
        ]);
    }
}