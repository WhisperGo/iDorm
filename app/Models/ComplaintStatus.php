<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintStatus extends Model
{
    protected $table = 'complaint_statuses';
    protected $guarded = ['id'];

    public function buildingComplaints()
    {
        return $this->hasMany(BuildingComplaint::class, 'status_id');
    }
}
