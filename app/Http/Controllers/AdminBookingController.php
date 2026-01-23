<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();
        
        // 1. Ambil data dasar dengan Eager Loading (Mencegah N+1)
        $query = Booking::with(['user.residentDetails', 'facility', 'status']);

        // 2. LOGIKA AUTO-FILTER BERDASARKAN URL
        // Kita cek URL-nya, misal: /view/dapur atau /view/mesin-cuci
        $path = $request->path(); 

        if (Str::contains($path, 'dapur')) {
            $query->whereHas('facility', fn($q) => $q->where('name', 'LIKE', '%Dapur%'));
            $title = "Manajemen Dapur";
        } elseif (Str::contains($path, 'mesin-cuci')) {
            $query->whereHas('facility', fn($q) => $q->where('name', 'LIKE', '%Mesin Cuci%'));
            $title = "Manajemen Mesin Cuci";
        } elseif (Str::contains($path, 'teater')) {
            $query->whereHas('facility', fn($q) => $q->where('name', 'LIKE', '%Teater%'));
            $title = "Manajemen Teater";
        } elseif (Str::contains($path, 'cws')) {
            $query->whereHas('facility', fn($q) => $q->where('name', 'LIKE', '%Co-Working Space%'));
            $title = "Manajemen CWS";
        } elseif (Str::contains($path, 'sergun')) {
            $query->whereHas('facility', fn($q) => $q->where('name', 'LIKE', '%Serba Guna%'));
            $title = "Manajemen Serba Guna";
        } else {
            $title = "Semua Peminjaman";
        }

        // 3. Tambahkan fitur pencarian nama penghuni jika ada input search
        if ($request->filled('search')) {
            $query->whereHas('user.residentDetails', function($q) use ($request) {
                $q->where('full_name', 'LIKE', "%{$request->search}%");
            });
        }

        if (auth()->user()->role->role_name === 'Resident') {
        abort(403, 'Anda tidak punya akses untuk menyetujui peminjaman.');
        }

        $bookings = $query->latest()->paginate(10);
        
        // 4. Ambil ID fasilitas yang boleh dikelola Admin ini (PIC Logic)
        $managedIds = $admin->managedFacilities->pluck('id')->toArray();

        return view('admin.booking_manage', compact('bookings', 'managedIds', 'title'));
    }
}