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
        // Ini akan otomatis bikin 25 data pengumuman bahasa Indonesia
        Announcement::factory()->count(25)->create();
    }
}