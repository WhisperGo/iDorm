<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'name',
        // 'email',
        'password',
        'account_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        // File: app/Models/User.php
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function residentDetails(): HasOne {
        return $this->hasOne(ResidentDetail::class, 'user_id');
    }

    public function managerDetails(): HasOne {
        return $this->hasOne(ManagerDetail::class, 'user_id');
    }

    public function adminDetails(): HasOne{
        return $this->hasOne(AdminDetail::class,'user_id');
    }

    // File: app/Models/ResidentDetail.php
    public function user() {
        return $this->belongsTo(User::class);
    }

    // app/Models/User.php
    public function managedFacilities(): BelongsToMany {
        return $this->belongsToMany(Facility::class, 'facility_admins', 'user_id', 'facility_id');
    }

    // app/Models/Booking.php
    public function facility(): BelongsTo {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function activeSuspensions(){
        // Mengambil data suspend yang statusnya aktif (tanggal valid)
        return $this->hasMany(Suspension::class)->active(); 
    }
}
