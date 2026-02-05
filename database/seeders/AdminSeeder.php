<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Facility;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminData = [
            // DAPUR
            ['card_id' => '1001', 'name' => 'Siti Admin Dapur 1', 'facility_id' => '1', 'room' => 'A101', 'gender' => 'Female', 'class' => 'PPTI 21'],
            ['card_id' => '1006', 'name' => 'Ani Admin Dapur 2', 'facility_id' => '1', 'room' => 'A102', 'gender' => 'Female', 'class' => 'PPTI 21'],
            // MESIN CUCI
            ['card_id' => '1002', 'name' => 'Bambang Admin MC 1', 'facility_id' => '2', 'room' => 'B202', 'gender' => 'Male', 'class' => 'PPTI 22'],
            ['card_id' => '1007', 'name' => 'Joko Admin MC 2', 'facility_id' => '2', 'room' => 'B203', 'gender' => 'Male', 'class' => 'PPTI 22'],
            // THEATER
            ['card_id' => '1003', 'name' => 'Rian Admin Theater 1', 'facility_id' => '3', 'room' => 'B301', 'gender' => 'Male', 'class' => 'PPTI 23'],
            ['card_id' => '1008', 'name' => 'Dimas Admin Theater 2', 'facility_id' => '3', 'room' => 'B302', 'gender' => 'Male', 'class' => 'PPTI 23'],
            // CWS
            ['card_id' => '1004', 'name' => 'Dewi Admin CWS 1', 'facility_id' => '4', 'room' => 'A201', 'gender' => 'Female', 'class' => 'PPTI 24'],
            ['card_id' => '1009', 'name' => 'Maya Admin CWS 2', 'facility_id' => '4', 'room' => 'A202', 'gender' => 'Female', 'class' => 'PPTI 24'],
            // SERGUN
            ['card_id' => '1005', 'name' => 'Eko Admin Sergun 1', 'facility_id' => '5', 'room' => 'B401', 'gender' => 'Male', 'class' => 'PPTI 25'],
            ['card_id' => '1010', 'name' => 'Budi Admin Sergun 2', 'facility_id' => '5', 'room' => 'B402', 'gender' => 'Male', 'class' => 'PPTI 25'],
        ];

        $dapurId = Facility::where('name', 'Dapur')->first()->id;
        foreach ($adminData as $data) {
            $user = User::create([
                'role_id' => 2,
                'card_id' => $data['card_id'],
                'password' => Hash::make('password'),
                'account_status' => 'active',
            ]);
            $user->adminDetails()->create([
                'full_name' => $data['name'],
                'gender' => $data['gender'],
                'room_number' => $data['room'],
                'class_name' => $data['class'],
                'facility_id' => $data['facility_id']
            ]);
        }
    }
}
