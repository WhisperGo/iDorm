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
        // Data manual 35 Resident - Kelas PPTI 22 - Card ID 0720 s/d 0754
        $residents = [
            ['id' => '0720', 'name' => 'Agnes Gonxha Febriane Sukma', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'B501'],
            ['id' => '0721', 'name' => 'Akbar Eka Putra', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B502'],
            ['id' => '0722', 'name' => 'Alfredo Putu Setyanugraha Atmaja', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A101'],
            ['id' => '0723', 'name' => 'Andi Zulfikar', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B201'],
            ['id' => '0724', 'name' => 'Andreas Calvin Hartono', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A102'],
            ['id' => '0725', 'name' => 'Ardika Hidayatur Rohman', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B301'],
            ['id' => '0726', 'name' => 'Bioline Alexandri Hendra Santosa', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'A205'],
            ['id' => '0727', 'name' => 'Calvin Martin', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B404'],
            ['id' => '0728', 'name' => 'Edwin Hendly', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A302'],
            ['id' => '0729', 'name' => 'Felicia Wijaya', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'B102'],
            ['id' => '0730', 'name' => 'Fino Wildan Ramadan', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B503'],
            ['id' => '0731', 'name' => 'I Gusti Bagus Arya Siwandana Janatha', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A103'],
            ['id' => '0732', 'name' => 'I Gusti Rai Hazel Nakhwah Handrata', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B202'],
            ['id' => '0733', 'name' => 'Imanuel Yusuf Setio Budi', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A401'],
            ['id' => '0734', 'name' => 'Jason Wijaya', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B105'],
            ['id' => '0735', 'name' => 'Jessie La Vonna Sanjaya', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'A501'],
            ['id' => '0736', 'name' => 'Joy Rochelle Kartolo', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'B305'],
            ['id' => '0737', 'name' => 'Kaleb Lister', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A202'],
            ['id' => '0738', 'name' => 'Keisha Grace Kristian', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'B402'],
            ['id' => '0739', 'name' => 'Kenny Wijaya', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A201'],
            ['id' => '0740', 'name' => 'Kevin Fernando', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B505'],
            ['id' => '0741', 'name' => 'Lidya Laura Sutanto', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'A105'],
            ['id' => '0742', 'name' => 'Lucia Sherina Natalia Kristianti', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'B405'],
            ['id' => '0743', 'name' => 'Maria Princessilia', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'A402'],
            ['id' => '0744', 'name' => 'Matthew Anderson', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B108'],
            ['id' => '0745', 'name' => 'Randysta Rasta Putra', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A108'],
            ['id' => '0746', 'name' => 'Rangga Mulia Tohpati', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B508'],
            ['id' => '0747', 'name' => 'Steven Jayadi Wiyanto', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A508'],
            ['id' => '0748', 'name' => 'Stieven Lee', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B308'],
            ['id' => '0749', 'name' => 'Theofrolic Anathapindika Dean', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B208'],
            ['id' => '0750', 'name' => 'Theresa Adelia Christi', 'gender' => 'Female', 'class' => 'PPTI 22', 'room' => 'A208'],
            ['id' => '0751', 'name' => 'Timotheus Edward Setiawan', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B408'],
            ['id' => '0752', 'name' => 'William Fernando Sukemi', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A408'],
            ['id' => '0753', 'name' => 'William Pratama', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'B209'],
            ['id' => '0754', 'name' => 'Yoyada Indrayudha', 'gender' => 'Male', 'class' => 'PPTI 22', 'room' => 'A509'],
        ];

        // Jalankan Loop untuk data MANUAL
        foreach ($residents as $res) {
            $user = User::create([
                'role_id' => 3,
                'card_id' => $res['id'],
                'password' => Hash::make('password'),
                'account_status' => 'active',
            ]);

            $user->residentDetails()->create([
                'full_name' => $res['name'],
                'gender' => $res['gender'],
                'class_name' => $res['class'],
                'room_number' => $res['room'],
            ]);
        }

        $this->command->info('35 Data Manual berhasil dimasukkan!');

        
        // Daftar kemungkinan kelas
        
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
        
        $faker = Faker::create('id_ID'); // Menggunakan locale Indonesia agar nama terlihat lokal
        $classes = ['PPTI 20', 'PPTI 21', 'PPTI 22', 'PPTI 23', 'PPTI 24', 'PPTI 25', 'PPBP 07', 'PPBP 08', 'PPBP 09', 'PPBP 10'];

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