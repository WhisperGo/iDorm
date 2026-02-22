<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Menampilkan halaman profil
    public function edit()
    {
        $user = User::with(['residentDetails', 'adminDetails', 'managerDetails'])->findOrFail(Auth::id());

        return view('feature.edit_profile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Cari User
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();
        $targetRole = $targetUser->role->role_name;

        // Tambahkan tanda tanya (?) setelah role untuk jaga-jaga kalau user gak punya role
        if (($currentUser->role?->role_name !== 'Manager') && ($currentUser->id !== (int) $id)) {
            abort(403, 'Anda tidak punya izin mengedit data orang lain.');
        }

        // Cek yang login apakah merupakan admin atau user
        $currentUserRole = $currentUser->role->role_name ?? '';
        $isRestricted = in_array($currentUserRole, ['Resident', 'Admin']);

        $rules = [
            'password' => 'nullable|min:8|confirmed', // Nullable = Opsional
        ];

        // Jika yang login MANAGER, dia bisa edit profil
        if ($currentUser->role->role_name === 'Manager') {
            $rules['full_name'] = 'required|string|max:255';
            $rules['phone_number'] = 'nullable|string|max:15';

            // HANYA wajibkan kelas jika yang DIEDIT adalah Resident
            if ($targetRole === 'Resident') {
                $rules['class_name'] = 'required|string|max:50';
            }
        }

        // if (!$isRestricted) {
        //     $rules['full_name']  = 'required|string|max:255';
        //     $rules['class_name'] = 'required|string|max:50';
        // }

        $request->validate($rules);

        // 2. Logika Update Password (Berlaku untuk SEMUA Role)
        if ($request->filled('password')) {
            $targetUser->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 4. Update Detail Berdasarkan Tabel Masing-masing
        if ($currentUser->role->role_name === 'Manager') {
            $data = $request->only(['full_name', 'phone_number']);

            // Handle Foto
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('profiles', 'public');
                $data['photo_path'] = $path;
            }

            // Simpan ke tabel yang sesuai
            if ($targetRole === 'Resident') {
                $data['class_name'] = $request->class_name;
                $targetUser->residentDetails()->updateOrCreate(['user_id' => $id], $data);
            } elseif ($targetRole === 'Admin') {
                $targetUser->adminDetails()->updateOrCreate(['user_id' => $id], $data);
            } elseif ($targetRole === 'Manager') {
                $targetUser->managerDetails()->updateOrCreate(['user_id' => $id], $data);
            }
        }

        return redirect()->back()->with('success', 'Perubahan berhasil disimpan!');
    }

    // Hanya untuk update Password
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Jika yang login adalah Resident/Admin, mereka hanya boleh update password
        if ($currentUser->hasRole(['resident', 'admin'])) {
            $request->validate([
                'password' => 'nullable|min:8|confirmed',
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            return redirect()->back()->with('success', 'Password berhasil diperbarui!');
        }

        // Jika SuperAdmin/Pengelola, baru jalankan logika update semua field...
    }
}