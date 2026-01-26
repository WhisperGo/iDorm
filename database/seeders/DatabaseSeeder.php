<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Facility;
use App\Models\BookingStatus;
use App\Models\ComplaintStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        // 1. SEED ROLES (ID: 1, 2, 3)
        $roles = ['Manager', 'Admin', 'Resident'];
        foreach ($roles as $r) {
            Role::create(['role_name' => $r]);
        }

        // 2. SEED STATUSES (Agar fitur Booking & Complaint tidak error)
        $bStatuses = ['Booked', 'Ongoing', 'Completed', 'Cancelled'];
        foreach ($bStatuses as $bs) {
            BookingStatus::create(['status_name' => $bs]);
        }

        $cStatuses = ['Submitted', 'On Progress', 'Resolved'];
        foreach ($cStatuses as $cs) {
            ComplaintStatus::create(['status_name' => $cs]);
        }

        // 3. SEED FACILITIES (Termasuk Filter Gender Mesin Cuci)
        $facilities = [
            ['name' => 'Dapur', 'type' => 'light'],
            ['name' => 'Mesin Cuci Male 1', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Male 2', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Male 3', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Male 4', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Male 5', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Female 1', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Female 2', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Female 3', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Female 4', 'type' => 'heavy'],
            ['name' => 'Mesin Cuci Female 5', 'type' => 'heavy'],
            ['name' => 'Theater Room', 'type' => 'light'],
            ['name' => 'Co-Working Space A', 'type' => 'light'],
            ['name' => 'Serba Guna Hall', 'type' => 'light'],
        ];
        foreach ($facilities as $f) {
            Facility::create($f);
        }

        // 4. SEED USERS (Login menggunakan card_id 4 digit)
        
        // A. PENGELOLA (MANAGER)
        $manager = User::create([
            'role_id' => 1,
            'card_id' => '0001',
            'password' => Hash::make('password'),
            'account_status' => 'active',
        ]);
        $manager->managerDetails()->create([
            'full_name' => 'Bp. Budi Pengelola',
            'gender' => 'Male',
            'phone_number' => '08123456789',
        ]);

        // B. ADMIN PIC (ADMIN)
        $admin = User::create([
            'role_id' => 2,
            'card_id' => '0002',
            'password' => Hash::make('password'),
        ]);
        $admin->managerDetails()->create([
            'full_name' => 'Siti Admin Dapur',
            'gender' => 'Female',
        ]);

        // C. PENGHUNI PRIA (RESIDENT MALE)
        $maleRes = User::create([
            'role_id' => 3,
            'card_id' => '1111',
            'password' => Hash::make('password'),
        ]);
        $maleRes->residentDetails()->create([
            'full_name' => 'Jason Wijaya',
            'gender' => 'Male',
            'class_name' => 'PPTI 20', // Sesuai Regex
            'room_number' => 'B332',  // Sesuai Regex
        ]);

        // D. PENGHUNI WANITA (RESIDENT FEMALE)
        $femaleRes = User::create([
            'role_id' => 3,
            'card_id' => '2222',
            'password' => Hash::make('password'),
        ]);
        $femaleRes->residentDetails()->create([
            'full_name' => 'Siska Rose',
            'gender' => 'Female',
            'class_name' => 'PPTI 21',
            'room_number' => 'A105',
        ]);

        // --- TAMBAHKAN DI SINI ---
        // Memanggil Seeder Tambahan
        $this->call([
            AnnouncementSeeder::class,
            TimeSlotSeeder::class,
        ]);
        // -------------------------

        echo "iDorm Database Seeded Successfully! \n";
    }
}