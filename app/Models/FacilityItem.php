<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityItem extends Model
{
    protected $fillable = [
        'facility_id',
        'name',
    ];

    public function partFacility(): BelongsTo{
        return $this->belongsTo(Facility::class);
    }
}
