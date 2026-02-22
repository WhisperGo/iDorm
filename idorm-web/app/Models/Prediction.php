<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'region',
        'harga',
        'luas_kamar',
        'latitude',
        'longitude',
        'tipe_kos',
        'is_km_dalam',
        'is_water_heater',
        'is_furnished',
        'is_listrik_free',
        'is_parkir_mobil',
        'is_mesin_cuci'
    ];
}