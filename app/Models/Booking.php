<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
    'user_id',
    'facility_id',
    'item_dapur',
    'item_sergun',
    'status_id',
    'slot_id',
    'booking_date',
    'start_time',
    'end_time',
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

    // Relasi ke Status Booking
    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }
}
