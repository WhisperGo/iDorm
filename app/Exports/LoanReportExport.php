<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LoanReportExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function query() {
        $query = Booking::query()->with(['user.residentDetails', 'facility', 'status']);

        // Filter berdasarkan Fasilitas (dari kartu yang diklik)
        if ($this->request->filled('facility_id')) {
            $query->where('facility_id', $this->request->facility_id);
        }

        // Filter berdasarkan Tanggal
        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('booking_date', [$this->request->start_date, $this->request->end_date]);
        }

        return $query->latest();
    }

    public function headings(): array {
        return ["No", "Nama Penghuni", "Fasilitas", "Tanggal", "Waktu", "Status Peminjaman", "Kebersihan"];
    }

    public function map($booking): array {
        static $no = 0;
        return [
            ++$no,
            $booking->user->residentDetails->full_name ?? $booking->user->name,
            $booking->facility->name,
            \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y'),
            substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5),
            $booking->status->status_name,
            ucfirst($booking->cleanliness_status),
        ];
    }
}