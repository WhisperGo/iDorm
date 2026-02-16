<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suspension extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suspensions';

    protected $fillable = [
        'user_id',      // Resident yang kena suspend
        'facility_id',  // Fasilitas (NULL = Global/Semua Fasilitas)
        'issued_by',    // Admin/Manager yang melakukan suspend
        'reason',       // Alasan suspend
        'start_date',
        'end_date',
    ];

    // Agar start_date & end_date otomatis jadi Carbon object
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    // Resident yang terkena hukuman
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Fasilitas yang disuspend (Bisa NULL)
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    // Admin/Manager yang memberikan hukuman
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // --- SCOPES (Shortcut Query) ---

    /**
     * Scope untuk mengambil data suspend yang SEDANG BERLAKU saat ini.
     * Cara pakai: Suspension::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                    ->orWhereNull('end_date'); // Null berarti selamanya/belum ditentukan
            });
    }

    /**
     * Scope untuk cek suspend global (level Pengelola)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('facility_id');
    }
}