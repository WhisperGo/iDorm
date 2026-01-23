<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BuildingComplaint;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Contoh: Mengambil statistik untuk Manager
        $totalBookings = Booking::count();
        $totalComplaints = BuildingComplaint::count();
        
        return view('admin.complaint', compact('totalBookings', 'totalComplaints'));
    }
}