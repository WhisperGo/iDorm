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
        // 1. SEED ROLES
        $roles = ['Manager', 'Admin', 'Resident'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['role_name' => $r]);
        }

        // 2. SEED STATUSES (Update sesuai alur baru)
        // ID: 1 (Booked), 2 (Accepted), 3 (Canceled), 4 (Verifying), 5 (Completed)
        $bStatuses = ['Booked', 'Accepted', 'Canceled', 'Verifying', 'Completed'];
        foreach ($bStatuses as $bs) {
            BookingStatus::firstOrCreate(['status_name' => $bs]);
        }

        $cStatuses = ['Submitted', 'On Progress', 'Resolved'];
        foreach ($cStatuses as $cs) {
            ComplaintStatus::firstOrCreate(['status_name' => $cs]);
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
            Facility::firstOrCreate(['name' => $f['name']], $f);
        }

        // 4. SEED ADMIN PER FASILITAS (Role ID: 2)
        // Pastikan kolom 'assigned_category' sudah ada di migrasi users kamu
        $adminData = [
            [
                'card_id' => '1001',
                'name' => 'Admin Dapur (Siti)',
                'category' => 'dapur'
            ],
            [
                'card_id' => '1002',
                'name' => 'Admin Laundry (Bambang)',
                'category' => 'mesin_cuci'
            ],
            [
                'card_id' => '1003',
                'name' => 'Admin Theater (Rian)',
                'category' => 'theater'
            ],
            [
                'card_id' => '1004',
                'name' => 'Admin CWS (Dewi)',
                'category' => 'cws'
            ],
            [
                'card_id' => '1005',
                'name' => 'Admin Sergun (Eko)',
                'category' => 'sergun'
            ],
        ];

        foreach ($adminData as $data) {
            $user = User::create([
                'role_id' => 2, // Role Admin
                'card_id' => $data['card_id'],
                'password' => Hash::make('password'),
                'assigned_category' => $data['category'], // KOLOM PENTING UNTUK OTORITAS
                'account_status' => 'active',
            ]);

            $user->managerDetails()->create([
                'full_name' => $data['name'],
                'gender' => ($data['card_id'] == '1004') ? 'Female' : 'Male',
            ]);
        }

        // 5. SEED PENGHUNI (RESIDENT)
        $maleRes = User::create([
            'role_id' => 3,
            'card_id' => '1111',
            'password' => Hash::make('password'),
        ]);
        $maleRes->residentDetails()->create([
            'full_name' => 'Jason Wijaya',
            'gender' => 'Male',
            'class_name' => 'PPTI 20',
            'room_number' => 'B332',
        ]);

        $user2 = User::create([
            'role_id' => 3,
            'card_id' => '1112', // Pastikan card_id unik jika di database diset UNIQUE
            'password' => Hash::make('password'),
        ]);
        $user2->residentDetails()->create([
            'full_name' => 'Wilep Pernando',
            'gender' => 'Male',
            'class_name' => 'PPTI 22',
            'room_number' => 'A321',
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
            BookingSeeder::class,
        ]);
        // -------------------------

        echo "iDorm Database Seeded Successfully! \n";
    }
}