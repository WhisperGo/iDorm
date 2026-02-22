<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Pengumuman dibuat oleh siapa (User/Admin)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
