<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan locale Indonesia agar nama terlihat lokal

        // Daftar kemungkinan kelas
        $classes = ['PPTI 20', 'PPTI 21', 'PPTI 22', 'PPTI 23', 'PPTI 24', 'PPTI 25', 'PPBP 07', 'PPBP 08', 'PPBP 09', 'PPBP 10'];

        // // 5. SEED PENGHUNI (RESIDENT)
        // $maleRes = User::create([
        //     'role_id' => 3,
        //     'card_id' => '1111',
        //     'password' => Hash::make('password'),
        // ]);
        // $maleRes->residentDetails()->create([
        //     'full_name' => 'Jason Wijaya',
        //     'gender' => 'Male',
        //     'class_name' => 'PPTI 20',
        //     'room_number' => 'B332',
        // ]);

        // $user2 = User::create([
        //     'role_id' => 3,
        //     'card_id' => '1112', // Pastikan card_id unik jika di database diset UNIQUE
        //     'password' => Hash::make('password'),
        // ]);
        // $user2->residentDetails()->create([
        //     'full_name' => 'Wilep Pernando',
        //     'gender' => 'Male',
        //     'class_name' => 'PPTI 22',
        //     'room_number' => 'A321',
        // ]);

        // // D. PENGHUNI WANITA (RESIDENT FEMALE)
        // $femaleRes = User::create([
        //     'role_id' => 3,
        //     'card_id' => '2222',
        //     'password' => Hash::make('password'),
        // ]);
        // $femaleRes->residentDetails()->create([
        //     'full_name' => 'Siska Rose',
        //     'gender' => 'Female',
        //     'class_name' => 'PPTI 21',
        //     'room_number' => 'A105',
        // ]);

        for ($i = 0; $i < 100; $i++) {
            $gender = $faker->randomElement(['Male', 'Female']);

            // 1. Buat User
            $user = User::create([
                'role_id' => 3, // Role Resident
                // Card ID unik mulai dari 3001 agar tidak bentrok dengan Admin/Manager
                'card_id' => str_pad(3001 + $i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'account_status' => 'active',
            ]);

            // 2. Buat Detail Penghuni
            // Logika Room Number: [Gedung A/B][Lantai 1-5][Nomor Kamar 01-40]
            $building = ($gender === 'Male') ? 'B' : 'A'; // Contoh: Cowok di Gedung B, Cewek di Gedung A
            $floor = $faker->numberBetween(1, 5);
            $roomNum = str_pad($faker->numberBetween(1, 40), 2, '0', STR_PAD_LEFT);
            $roomNumber = $building . $floor . $roomNum;

            $user->residentDetails()->create([
                'full_name' => ($gender === 'Male') ? $faker->name('male') : $faker->name('female'),
                'gender' => $gender,
                'class_name' => $faker->randomElement($classes),
                'room_number' => $roomNumber,
                'phone_number' => '8' . $faker->numberBetween(1000000000, 9999999999),
            ]);
        }
    }
}