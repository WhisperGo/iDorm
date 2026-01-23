<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Relasi: Satu fasilitas punya banyak booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
