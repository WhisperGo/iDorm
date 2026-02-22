<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResidentController extends Controller
{
    // Konstanta untuk menghindari hardcode string di banyak tempat
    const ROLE_RESIDENT = 'Resident';
    const ROLE_ADMIN = 'Admin';
    const ROLE_MANAGER = 'Manager';
    const FACILITY_LAUNDRY = 'mesin cuci'; // Keyword untuk deteksi admin laundry

    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Eager load role untuk efisiensi
        if (!$currentUser->relationLoaded('role')) {
            $currentUser->load('role');
        }

        $currentRoleName = $currentUser->role->role_name ?? '';

        // 1. Authorization Check (Fail Fast)
        if ($currentRoleName === self::ROLE_RESIDENT) {
            abort(403, 'Unauthorized access.');
        }

        // 2. Setup Variables
        $search = $request->get('search');
        $isManager = $currentRoleName === self::ROLE_MANAGER;
        $isAdmin = $currentRoleName === self::ROLE_ADMIN;

        // Ambil detail admin sekali saja
        $adminDetails = $currentUser->adminDetails;
        $myFacilityId = $adminDetails?->facility_id;
        $myFacilityName = strtolower($adminDetails?->facilities?->name ?? '');
        $adminGender = $adminDetails?->gender;

        // Cek apakah ini Admin Mesin Cuci (Logic Safe Mode)
        $isLaundryAdmin = $isAdmin && str_contains($myFacilityName, self::FACILITY_LAUNDRY);

        // 3. Fasilitas untuk Dropdown (Hanya Manager butuh semua, Admin butuh miliknya saja untuk info)
        // Namun karena modal membutuhkan list, kita tarik semua saja agar aman.
        $facilities = Facility::all();

        // 4. Query Resident
        $query = User::with(['residentDetails', 'activeSuspensions'])
            ->whereHas('role', function ($q) {
                $q->where('role_name', self::ROLE_RESIDENT);
            });

        // 4a. Apply Search
        if ($search) {
            $query->whereHas('residentDetails', function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('room_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%");
            })->orWhere('card_id', 'LIKE', "%{$search}%");
        }

        // 4b. Apply Logic "Safe Mode" (Filter di Database Level)
        // Jika admin laundry, HANYA tarik data resident yang gendernya sama.
        if ($isLaundryAdmin && $adminGender) {
            $query->whereHas('residentDetails', function ($q) use ($adminGender) {
                $q->where('gender', $adminGender);
            });
        }

        // 5. Execute Query
        $residents = $query->latest()->paginate(10);

        return view('feature.resident_management', compact(
            'residents',
            'facilities',
            'isManager',
            'isAdmin',
            'isLaundryAdmin',
            'myFacilityId',
            'myFacilityName',
            'adminGender',
            'currentUser'
        ));
    }
}