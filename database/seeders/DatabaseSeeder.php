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

        // PENGELOLA (MANAGER)
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

        // 4. SEED ADMIN PER FASILITAS (Role ID: 2)
        // Pastikan kolom 'assigned_category' sudah ada di migrasi users kamu
        // 5. SEED ADMIN FASILITAS (Minimal 2 Orang Per Fasilitas)
        $adminData = [
            // DAPUR
            ['card_id' => '1001', 'name' => 'Siti Admin Dapur 1', 'category' => 'dapur', 'room' => 'A101', 'gender' => 'Female', 'class' => 'PPTI 21'],
            ['card_id' => '1006', 'name' => 'Ani Admin Dapur 2', 'category' => 'dapur', 'room' => 'A102', 'gender' => 'Female', 'class' => 'PPTI 21'],
            // MESIN CUCI
            ['card_id' => '1002', 'name' => 'Bambang Admin MC 1', 'category' => 'mesin_cuci', 'room' => 'B202', 'gender' => 'Male', 'class' => 'PPTI 22'],
            ['card_id' => '1007', 'name' => 'Joko Admin MC 2', 'category' => 'mesin_cuci', 'room' => 'B203', 'gender' => 'Male', 'class' => 'PPTI 22'],
            // THEATER
            ['card_id' => '1003', 'name' => 'Rian Admin Theater 1', 'category' => 'theater', 'room' => 'B301', 'gender' => 'Male', 'class' => 'PPTI 23'],
            ['card_id' => '1008', 'name' => 'Dimas Admin Theater 2', 'category' => 'theater', 'room' => 'B302', 'gender' => 'Male', 'class' => 'PPTI 23'],
            // CWS
            ['card_id' => '1004', 'name' => 'Dewi Admin CWS 1', 'category' => 'cws', 'room' => 'A201', 'gender' => 'Female', 'class' => 'PPTI 24'],
            ['card_id' => '1009', 'name' => 'Maya Admin CWS 2', 'category' => 'cws', 'room' => 'A202', 'gender' => 'Female', 'class' => 'PPTI 24'],
            // SERGUN
            ['card_id' => '1005', 'name' => 'Eko Admin Sergun 1', 'category' => 'sergun', 'room' => 'B401', 'gender' => 'Male', 'class' => 'PPTI 25'],
            ['card_id' => '1010', 'name' => 'Budi Admin Sergun 2', 'category' => 'sergun', 'room' => 'B402', 'gender' => 'Male', 'class' => 'PPTI 25'],
        ];

        foreach ($adminData as $data) {
            $user = User::create([
                'role_id' => 2,
                'card_id' => $data['card_id'],
                'password' => Hash::make('password'),
                'assigned_category' => $data['category'],
                'account_status' => 'active',
            ]);
            $user->residentDetails()->create([
                'full_name' => $data['name'],
                'gender' => $data['gender'],
                'room_number' => $data['room'],
                'class_name' => $data['class']
            ]);
        }

        // foreach ($adminData as $data) {
        //     $user = User::create([
        //         'role_id' => 2, // Role Admin
        //         'card_id' => $data['card_id'],
        //         'password' => Hash::make('password'),
        //         'assigned_category' => $data['category'], // KOLOM PENTING UNTUK OTORITAS
        //         'account_status' => 'active',
        //     ]);
        // }

        // --- TAMBAHKAN DI SINI ---
        // Memanggil Seeder Tambahan
        $this->call([
            ResidentSeeder::class,
            AnnouncementSeeder::class,
            TimeSlotSeeder::class,
            BookingSeeder::class,
        ]);
        // -------------------------

        echo "iDorm Database Seeded Successfully! \n";
    }
}