<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManagerDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Relasi balik: Detail ini milik user siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
