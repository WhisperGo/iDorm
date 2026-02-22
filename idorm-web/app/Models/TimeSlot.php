<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel di database
    protected $table = 'time_slots';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'facilities',
        'start_time',
        'end_time',
    ];

    /**
     * Relasi ke Tabel Booking
     * Satu slot waktu bisa dimiliki oleh banyak booking (di tanggal yang berbeda)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'slot_id');
    }

    /**
     * Helper: Menampilkan format jam yang cantik di View
     * Contoh: "08:00 - 10:00"
     */
    public function getFullSlotAttribute()
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }
}