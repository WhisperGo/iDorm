<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class PengelolaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function residentIndex()
    {
        $title = "Resident Management";
        $subtitle = "View and manage all residents in the dormitory";
        
        // Ambil data user yang rolenya 'Resident'
        $residents = User::whereHas('role', function($q) {
            $q->where('role_name', 'Resident');
        })->with('residentDetails')->paginate(10);

        return view('pengelola.resident', compact('residents', 'title', 'subtitle'));
    }

    public function reportIndex()
    {
        $title = "Activity Reports";
        $subtitle = "Analyze facility usage statistics and reports";
        
        // Ambil data semua booking untuk laporan
        $reports = Booking::with(['user.residentDetails', 'facility', 'status'])
                    ->latest()
                    ->paginate(15);

        return view('pengelola.loan_report', compact('reports', 'title', 'subtitle'));
    }
}
