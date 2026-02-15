<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Mencegah N+1 Problem dengan Eager Loading 'role' dan 'residentDetails'
        $residents = User::with(['role', 'residentDetails'])
            ->whereHas('role', function ($q) {
                $q->where('role_name', 'Resident');
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('residentDetails', function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")->orWhere('room_number', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        if (auth()->user()->role->role_name === 'Resident') {
            abort(403); // Penghuni biasa tidak boleh buka list penghuni lain
        }

        return view('resident', compact('residents'));
    }

    public function toggleFreeze(User $user)
    {
        $newStatus = $user->account_status === 'active' ? 'frozen' : 'active';
        $user->update(['account_status' => $newStatus]);

        return back()->with('success', "Status akun berhasil diubah menjadi $newStatus");
    }
}