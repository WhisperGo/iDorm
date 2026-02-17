<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Announcement;
use App\Models\BuildingComplaint;
use App\Models\Facility;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // 1. Muncul saat pertama kali klik menu (Hanya Menu Pilihan)
    public function index()
    {
        $totalBookings = Booking::count();
        $totalComplaints = BuildingComplaint::count();
        $facilities = Facility::all(); // Ambil semua fasilitas untuk pilihan kartu
        
        // Kirim $bookings kosong agar Blade tidak error, atau gunakan flag
        return view('pengelola.loan_report', [
            'totalBookings' => $totalBookings,
            'totalComplaints' => $totalComplaints,
            'facilities' => $facilities,
            'bookings' => null, // Tandanya kita belum pilih fasilitas
            'title' => 'Pilih Fasilitas Laporan'
        ]);
    }

    // 2. Muncul setelah Klik Kartu atau Filter Tanggal
    public function reportIndex(Request $request)
    {
        $query = Booking::with(['user.residentDetails', 'facility', 'status']);
    
        // Filter Fasilitas
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }
    
        $bookings = $query->latest()->paginate(15)->withQueryString();
        $facilities = Facility::all();
        $totalBookings = Booking::count();
        $totalComplaints = BuildingComplaint::count();
    
        return view('pengelola.loan_report', [
            'bookings' => $bookings,
            'facilities' => $facilities,
            'totalBookings' => $totalBookings,
            'totalComplaints' => $totalComplaints,
            'title' => 'Detail Laporan Peminjaman'
        ]);
    }
}