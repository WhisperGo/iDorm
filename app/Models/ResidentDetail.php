<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class ResidentDetail extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    protected $fillable = [
    'user_id', 
    'full_name', 
    'gender', 
    'room_number', 
    'class_name', 
    'phone_number'
    ];

    protected $dates = ['deleted_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
