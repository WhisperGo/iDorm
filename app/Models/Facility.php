<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AdminDetail;
use App\Models\Booking;
use App\Models\FacilityItem;

class Facility extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Relasi: Satu fasilitas punya banyak booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function managedByAdmin(): HasMany{
        return $this->hasMany(AdminDetail::class);
    }

    public function facilityItems(): HasMany {
        return $this->hasMany(FacilityItem::class);
    }
}
