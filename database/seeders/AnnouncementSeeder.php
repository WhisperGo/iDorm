<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil user pertama yang memiliki role Manager (1) atau Admin (2)
        $admin = User::whereIn('role_id', [1, 2])->first();

        
        // Cek jika admin ditemukan agar tidak error saat seeding
        if ($admin) {
            for ($i = 1; $i <= 25; $i++) {
                Announcement::create([
                    'author_id' => $admin->id, // Masukkan ID Admin di sini
                    'title'     => $faker->sentence(rand(4, 8)),
                    'content'   => $faker->paragraphs(rand(3, 5), true),
                    'created_at'=> $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }
    }
}