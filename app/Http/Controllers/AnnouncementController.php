<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    // 1. Menampilkan daftar pengumuman (Bisa diakses SEMUA role)
    public function index()
    {
        // Ambil semua data pengumuman
        $announcements = Announcement::with('author')->latest()->paginate(10);
        
        // Kirim ke SATU file yang sama
        return view('template.announcement', compact('announcements'));
    }

    // 2. Form Tambah (Hanya Admin/Pengelola)
    public function create()
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403, 'Penghuni tidak diizinkan membuat pengumuman.');
        }
        return view('template.announcement'); // Pisahkan view form tambah
    }

    public function show($id)
    {
        // Cari pengumuman berdasarkan ID
        $announcement = Announcement::findOrFail($id);
    
        // Tampilkan ke view detail (buat file ini jika belum ada)
        return view('penghuni.announcement_detail', compact('announcement'));
    }

    // 3. Simpan ke Database (Hanya Admin/Pengelola)
    public function store(Request $request)
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('announcements')->with('success', 'Pengumuman berhasil diterbitkan!');
    }

    // 4. Form Edit (Hanya Admin/Pengelola)
    public function edit(Announcement $announcement)
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }
        return view('template.announcement', compact('announcement'));
    }

    // 5. Update Data (Hanya Admin/Pengelola)
    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $announcement->update($request->only(['title', 'content']));

        return redirect()->route('announcements')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    // 6. Hapus Data (Hanya Admin/Pengelola)
    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }
        
        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}