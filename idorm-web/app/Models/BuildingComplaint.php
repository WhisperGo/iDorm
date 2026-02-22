<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuildingComplaint extends Model
{
    use SoftDeletes;

    // Karena nama tabelmu 'building_complaints' (plural), 
    // Laravel biasanya sudah otomatis mengenali. Tapi amannya kita definisikan:
    protected $table = 'building_complaints';

    protected $fillable = [
        'resident_id',    // PASTIKAN INI ADA
        'location_item',
        'description',
        'status_id',
        'photo_path',
    ];

    // Relasi: Keluhan ini milik siapa (Resident)
    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    // Relasi: Status keluhan (Submitted, On Progress, Resolved)
    public function status()
    {
        return $this->belongsTo(ComplaintStatus::class, 'status_id');
    }
}
