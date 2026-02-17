<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Announcement;
use App\Models\BuildingComplaint;
use App\Models\Facility;
use Illuminate\Http\Request;

use App\Exports\LoanReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


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

    // Tambahkan di dalam class
    public function exportExcel(Request $request) 
    {
        return Excel::download(new LoanReportExport($request), 'laporan-iDorm-'.now()->format('Ymd').'.xlsx');
    }

    public function exportPdf(Request $request) 
    {
        ini_set('memory_limit', '512M'); // Tambah limit memori jadi 512MB
        set_time_limit(300);
        $query = Booking::with(['user.residentDetails', 'facility', 'status']);

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }

        $bookings = $query->latest()->get();
        $facility_name = $request->facility_id ? \App\Models\Facility::find($request->facility_id)->name : 'Semua Fasilitas';

        $pdf = Pdf::loadView('pengelola.pdf_report', compact('bookings', 'facility_name'));
        return $pdf->download('laporan-iDorm-'.now()->format('Ymd').'.pdf');
    }
}