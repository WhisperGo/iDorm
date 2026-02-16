<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $currentUser = Auth::user();

        // 1. Ambil Fasilitas (Untuk kebutuhan mapping ID di modal nanti)
        // Jika Manager: butuh semua (atau null). Jika Admin: butuh fasilitas dia.
        $facilities = Facility::all();

        // 2. Query Resident dengan Eager Loading 'activeSuspensions'
        $residents = User::with(['role', 'residentDetails', 'activeSuspensions'])
            ->whereHas('role', function ($q) {
                $q->where('role_name', 'Resident');
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('residentDetails', function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                      ->orWhere('room_number', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        if ($currentUser->role->role_name === 'Resident') {
            abort(403); 
        }

        return view('feature.resident', compact('residents', 'facilities'));
    }
}