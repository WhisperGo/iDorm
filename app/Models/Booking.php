<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Facility;
use App\Models\TimeSlot;
use App\Models\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $appends = ['calculated_status'];
    
    protected $fillable = [
        'user_id',
        'facility_id',
        'facility_item_id',
        'item_dapur',
        'item_sergun',
        'status_id',
        'slot_id',
        'booking_date',
        'start_time',
        'end_time',
        'description',
        'jumlah_orang',
        'photo_proof_path',
        'cleanliness_status',
    ];

    public function slot()
    {
        // Parameter kedua adalah foreign key di tabel bookings
        return $this->belongsTo(TimeSlot::class, 'slot_id');
    }

    // Relasi ke User (Penghuni)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Fasilitas
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function facilityItem(){
        return $this->belongsTo(FacilityItem::class,'facility_item_id');
    }

    // Relasi ke Status Booking
    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }

    // File: app/Models/Booking.php

    public function getCalculatedStatusAttribute()
    {
        $now = Carbon::now('Asia/Jakarta');

        $start = Carbon::parse($this->booking_date . ' ' . $this->start_time, 'Asia/Jakarta');
        $end = Carbon::parse($this->booking_date . ' ' . $this->end_time, 'Asia/Jakarta');

        // Pastikan relasi status diload
        if (!$this->relationLoaded('status')) {
            $this->load('status');
        }

        $statusName = $this->status->status_name ?? 'Booked';

        // --- PERBAIKAN DI SINI ---
        // Masukkan 'Verifying Cleanliness' ke dalam array ini.
        // Artinya: Jika status di DB adalah salah satu dari ini, JANGAN cek jam lagi. Langsung return statusnya.
        $ignoredStatuses = [
            'Completed', 
            'Canceled', 
            'Rejected', 
            'Verifying Cleanliness', // <--- PENTING! Tambahkan ini
            'Verifying' // Jaga-jaga kalau nama di DB cuma 'Verifying'
        ];

        if (in_array($statusName, $ignoredStatuses)) {
            return $statusName;
        }
        // -------------------------

        // Logika Time-Based hanya berlaku jika status masih 'Accepted'
        if ($statusName === 'Accepted') {
            if ($now->lt($start)) {
                return 'Upcoming'; // Atau biarkan 'Accepted'
            }

            // Sedang berjalan
            if ($now->between($start, $end)) {
                // Cek flag early release
                if ($this->is_early_release) {
                     return 'Awaiting Cleanliness Photo';
                }
                return 'On Going';
            }

            // Waktu sudah habis
            if ($now->gt($end)) {
                return 'Awaiting Cleanliness Photo';
            }
        }

        return $statusName; 
    }

    public function canReleaseEarly(){
        return $this->calculated_status === 'On Going';
    }
}
