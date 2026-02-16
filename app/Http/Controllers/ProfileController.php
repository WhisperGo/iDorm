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
        
        return view('feature.edit_profile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Data
        $request->validate([
            'full_name'  => 'required|string|max:255',
            'class_name' => 'required|string|max:50', // Wajib karena di form ada bintang merah
            'phone'      => 'nullable|string|max:15',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Tambahkan webp jika perlu
        ]);

        // Cari User
        $user = User::findOrFail($id);

        // 2. Siapkan data dasar yang akan diupdate/create
        // Kita gunakan only() agar lebih bersih daripada menunjuk satu per satu
        $dataDetails = $request->only(['full_name', 'class_name', 'phone']);

        // 3. Handle Foto Profile
        if ($request->hasFile('photo')) {
            // A. Hapus foto lama jika ada (PENTING: Cek dulu apakah file fisik benar-benar ada)
            if ($user->residentDetails && $user->residentDetails->photo_path) {
                if (Storage::disk('public')->exists($user->residentDetails->photo_path)) {
                    Storage::disk('public')->delete($user->residentDetails->photo_path);
                }
            }

            // B. Simpan foto baru dan masukkan path ke array dataDetails
            $path = $request->file('photo')->store('profiles', 'public');
            $dataDetails['photo_path'] = $path;
        }

        // 4. Eksekusi Simpan dengan updateOrCreate
        // Ini akan mencari resident_details milik user_id tersebut.
        // Jika ketemu -> Update. Jika tidak -> Buat baru.
        $user->residentDetails()->updateOrCreate(
            ['user_id' => $user->id], // Kondisi pencarian (Kunci Unik)
            $dataDetails              // Data yang disimpan/diupdate
        );

        // 5. Redirect kembali
        // Pastikan nama route 'admin.resident.index' sesuai dengan route list Anda
        return redirect()->route('admin.resident.index')
            ->with('success', 'Profil penghuni berhasil diperbarui!');
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