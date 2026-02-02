<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BuildingComplaint;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Contoh: Mengambil statistik untuk Manager
        $totalBookings = Booking::count();
        $totalComplaints = BuildingComplaint::count();
        
        return view('admin.complaint', compact('totalBookings', 'totalComplaints'));
    }

    public function reportIndex(Request $request)
    {
        // Ambil data dengan relasi agar tidak N+1 query
        $query = Booking::with(['user.residentDetails', 'facility', 'status']);
    
        // Tambahkan filter tanggal jika ada pencarian
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }
    
        $bookings = $query->latest()->paginate(15);
    
        return view('pengelola.loan_report', [
            'bookings' => $bookings,
            'title' => 'Laporan Peminjaman Fasilitas',
            'subtitle' => 'Daftar riwayat peminjaman seluruh penghuni iDorm.'
        ]);
    }
}