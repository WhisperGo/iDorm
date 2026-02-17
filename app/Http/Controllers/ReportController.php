<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BuildingComplaint;
use App\Models\Facility;
use Illuminate\Http\Request;
use App\Exports\LoanReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Tampilan awal (Landing) - Belum milih kartu
     */
    public function index()
    {
        return view('pengelola.loan_report', [
            'facilities' => Facility::all(),
            'totalBookings' => Booking::count(),
            'totalComplaints' => BuildingComplaint::count(),
            'bookings' => null, // Tabel tidak akan muncul
            'title' => 'Pilih Fasilitas Laporan'
        ]);
    }

    /**
     * Tampilan setelah milih kartu atau filter tanggal
     */
    public function reportIndex(Request $request)
    {
        $facilities = Facility::all();
        $bookings = null;

        // Cek apakah ada parameter facility_id
        if ($request->filled('facility_id')) {
            $query = Booking::with(['user.residentDetails', 'facility', 'status']);

            // LOGIKA ALL: Jika isinya bukan 'all', baru kita filter berdasarkan ID
            if ($request->facility_id !== 'all') {
                $query->where('facility_id', $request->facility_id);
            }

            // Filter Tanggal
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
            }

            $bookings = $query->latest()->paginate(15)->withQueryString();
        }

        return view('pengelola.loan_report', [
            'bookings' => $bookings,
            'facilities' => $facilities,
            'totalBookings' => Booking::count(),
            'totalComplaints' => BuildingComplaint::count(),
            'title' => 'Detail Laporan Peminjaman'
        ]);
    }

    /**
     * Export ke Excel
     */
    public function exportExcel(Request $request) 
    {
        return Excel::download(new LoanReportExport($request), 'laporan-iDorm-'.now()->format('Ymd').'.xlsx');
    }

    /**
     * Export ke PDF
     */
    public function exportPdf(Request $request) 
    {
        // Beri napas tambahan buat server
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // MULAI QUERY (Jangan langsung di-get())
        $query = Booking::with(['user.residentDetails', 'facility', 'status']);

        // Filter Fasilitas (Sama dengan reportIndex)
        if ($request->filled('facility_id') && $request->facility_id !== 'all') {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }

        // Ambil datanya
        $bookings = $query->latest()->get();

        // Penentuan nama fasilitas untuk header PDF
        $facility_name = ($request->facility_id && $request->facility_id !== 'all') 
            ? Facility::find($request->facility_id)->name 
            : 'Semua Fasilitas';

        $pdf = Pdf::loadView('pengelola.pdf_report', compact('bookings', 'facility_name'));
        return $pdf->download('laporan-iDorm-'.now()->format('Ymd').'.pdf');
    }
}