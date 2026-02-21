<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    // 1. Menampilkan daftar pengumuman (Bisa diakses SEMUA role)
    public function index(Request $request)
    {
        $query = Announcement::with('author')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', '%' . $search . '%');
        }

        $announcements = $query->paginate(10)->withQueryString();
        
        return view('feature.announcements.announcement', compact('announcements'));
    }

    // 2. Form Tambah (Hanya Admin/Pengelola)
    public function create()
    {
        $title = "Create Announcement";
        $subtitle = "Create a new announcement for residents.";

        if (Auth::user()->role->role_name === 'Resident') {
            abort(403, 'Penghuni tidak diizinkan membuat pengumuman.');
        }
        return view('feature.announcements.add_announcement', compact('title', 'subtitle')); // Pisahkan view form tambah
    }

    public function show($id)
    {
        // Cari pengumuman berdasarkan ID
        $announcement = Announcement::findOrFail($id);
    
        // Tampilkan ke view detail (buat file ini jika belum ada)
        return view('feature.announcements.announcement_detail', compact('announcement'));
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
        $title = "Edit Announcement";
        $subtitle = "Edit the announcement details.";
        
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }
        return view('feature.announcements.edit_announcement', compact('announcement', 'title', 'subtitle'));
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

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('announcements')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    // 6. Hapus Data (Hanya Admin/Pengelola)
    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->role->role_name === 'Resident') {
            abort(403);
        }
        
        $announcement->delete();

        return redirect()->route('announcements')->with('success', 'Pengumuman berhasil dihapus!');
    }
}