<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (Mass Assignment)
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'status_id',
        'description',
        'admin_feedback',
        'photo_path', // Jika kamu ada fitur upload foto bukti
    ];

    /**
     * Relasi ke User (Pemilik Keluhan)
     */
    public function user()
    {
        // Pastikan foreign key sesuai dengan yang ada di database kamu (user_id)
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kategori Keluhan
     */
    public function category()
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    /**
     * Relasi ke Status Keluhan (Pending, In Progress, Resolved, Rejected)
     */
    public function status()
    {
        return $this->belongsTo(ComplaintStatus::class, 'status_id');
    }
}