<?php

namespace App\Models;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'class_name',
        'room_number',
        'facility_id',
        'phone_number',
        'photo_path'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facilities(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id', );
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}
