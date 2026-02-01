<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role_name;
    
        // Ambil data pengumuman untuk semua role
        $announcements = Announcement::latest()->take(3)->get();
    
        // LOGIKA PEMILIHAN VIEW
        if ($role === 'Manager') {
            return view('pengelola.dashboard', compact('announcements'));
        }
        
        if ($role === 'Admin') {
            return view('admin.dashboard', compact('announcements'));
        }
    
        // Default untuk Resident (Penghuni)
        // Pastikan kamu punya file: resources/views/resident/dashboard.blade.php
        return view('penghuni.dashboard', compact('announcements'));
    }
}
