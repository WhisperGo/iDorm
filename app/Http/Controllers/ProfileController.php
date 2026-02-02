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
        $user = User::with('residentDetails')->findOrFail(Auth::id());
        
        return view('admin.editProfile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // 1. Validasi Data
        $request->validate([
            'full_name' => 'required|string|max:255',
            'class_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($id);

        // 2. Siapkan data yang akan diupdate
        $dataDetails = [
            'full_name' => $request->full_name,
            'class_name' => $request->class_name,
            'phone' => $request->phone,
        ];

        // 3. Handle Foto Profile
        if ($request->hasFile('photo')) {
            if ($user->residentDetails?->photo_path) {
                Storage::disk('public')->delete($user->residentDetails->photo_path);
            }
            $dataDetails['photo_path'] = $request->file('photo')->store('profiles', 'public');
        }

        // 4. Eksekusi Simpan (Gunakan updateOrCreate agar lebih aman)
        // Mencari berdasarkan user_id, jika ada maka update, jika tidak ada maka create.
        $user->residentDetails()->updateOrCreate(
            ['user_id' => $user->id],
            $dataDetails
        );

        return redirect()->route('admin.resident')->with('success', 'Data penghuni berhasil diperbarui!');
    }

    // Hanya untuk update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], // Validasi password lama
            'password' => ['required', 'confirmed', Password::defaults()], // Minimal 8 karakter
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}