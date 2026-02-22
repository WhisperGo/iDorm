<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        // Data sesi untuk fasilitas 'heavy' (Mesin Cuci)
        $slots = [
            ['start' => '00:00', 'end' => '02:00'],
            ['start' => '02:00', 'end' => '04:00'],
            ['start' => '04:00', 'end' => '06:00'],
            ['start' => '06:00', 'end' => '08:00'],
            ['start' => '08:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '18:00'],
            ['start' => '18:00', 'end' => '20:00'],
            ['start' => '20:00', 'end' => '22:00'],
            ['start' => '22:00', 'end' => '00:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::create([
                'facilities' => 'heavy',
                'start_time' => $slot['start'],
                'end_time'   => $slot['end'],
            ]);
        }
    }
}