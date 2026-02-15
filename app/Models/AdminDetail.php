<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Facility;

class AdminDetail extends Model
{
    // protected $fillable = [
    //     'user_id',
    //     'facility_id',
    //     'full_name',
    //     'gender',
    //     'class_name',
    //     'room_number',
    //     'phone_number',
    //     'photo_path',
    // ];

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facilities(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id', );
    }
}
