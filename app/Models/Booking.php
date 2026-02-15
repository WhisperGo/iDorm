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

    // Di dalam class Booking
    public function getCalculatedStatusAttribute()
        {
            $now = Carbon::now();
            $start = Carbon::parse($this->booking_date . ' ' . $this->start_time);
            $end = Carbon::parse($this->booking_date . ' ' . $this->end_time);
        
            // 1. Jika status sudah Completed atau Canceled, kembalikan status asli
            if (in_array($this->status->status_name, ['Completed', 'Canceled'])) {
                return $this->status->status_name;
            }
        
            // 2. Jika statusnya Accepted, cek waktunya
            if ($this->status->status_name === 'Accepted') {
                if ($now->between($start, $end)) {
                    return 'On Going';
                }
                if ($now->greaterThan($end)) {
                    return 'Awaiting Cleanliness Photo';
                }
            }
        
            // 3. Jika statusnya 'Verifying' (setelah upload foto)
            if ($this->status->status_name === 'Verifying') {
                return 'Verifying Cleanliness';
            }
        
            return $this->status->status_name; // Default: Booked
        }
}
