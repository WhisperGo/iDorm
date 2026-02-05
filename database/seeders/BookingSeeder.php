<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\TimeSlot;
use App\Models\FacilityItem;
use Faker\Factory as Faker;
use App\Models\BookingStatus;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $residents = User::whereHas(
            'role',
            fn($q) =>
            $q->whereIn('role_name', ['Resident', 'Admin'])
        )->with('residentDetails')->get();

        // Pastikan nama kolom di database kamu benar (facility_name atau name)
        $facilities = Facility::all();
        $slots = TimeSlot::all();

        if ($residents->isEmpty() || $facilities->isEmpty()) {
            $this->command->warn("Eits! Pastikan tabel Users dan Facilities sudah ada datanya.");
            return;
        }

        $this->command->info("Sedang mengisi 1000 data booking dummy...");

        for ($i = 0; $i < 1000; $i++) {
            $resident = $residents->random();
            $gender = $resident->residentDetails->gender ?? 'Male';

            $facility = $facilities->random();
            // 1. GUNAKAN LOWERCASE UNTUK SEMUA PERBANDINGAN
            $fname = strtolower($facility->facility_name ?? $facility->name);

            // 2. FIX QUERY: Cari berdasarkan facility_id, bukan id item
            $itemsQuery = FacilityItem::where('facility_id', $facility->id);

            if (str_contains($fname, 'mesin cuci')) {
                // Pastikan string gender sesuai dengan apa yang ada di seeder barang kamu
                $itemsQuery->where('name', 'like', "%{$gender}%");
            }

            $facilityItem = $itemsQuery->inRandomOrder()->first();

            // Jika item masih null, kita beri peringatan agar kamu tahu fasilitas mana yang kosong
            if (!$facilityItem) {
                $this->command->error("Fasilitas {$facility->facility_name} (ID: {$facility->id}) tidak punya barang! Seeding gagal untuk baris ini.");
                continue;
            }

            $randomDate = $faker->dateTimeBetween('-1 month', '+1 month');
            $date = Carbon::instance($randomDate);
            $dateStr = $date->format('Y-m-d');

            // --- LOGIKA STATUS ---
            if ($date->isPast()) {
                $statusId = rand(0, 1) ? 5 : 3;
                $cleanStatus = ($statusId == 5) ? 'approved' : 'pending';
            } elseif ($date->isToday()) {
                $statusId = rand(1, 4);
                $cleanStatus = 'pending';
            } else {
                $statusId = 1;
                $cleanStatus = 'pending';
            }

            // --- DATA DASAR ---
            $data = [
                'user_id' => $resident->id,
                'facility_id' => $facility->id,
                'facility_item_id' => $facilityItem->id,
                'booking_date' => $dateStr,
                'status_id' => $statusId,
                'cleanliness_status' => $cleanStatus,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // --- LOGIKA WAKTU (Sekarang If-nya pasti tembus karena lowercase) ---
            if (str_contains($fname, 'Mesin Cuci')) {
                $slot = $slots->random();
                $data['slot_id'] = $slot->id;
                $data['start_time'] = $slot->start_time;
                $data['end_time'] = $slot->end_time;
            } elseif (str_contains($fname, 'Dapur')) {
                $start = rand(10, 21);
                $data['start_time'] = sprintf("%02d:00:00", $start);
                $data['end_time'] = sprintf("%02d:30:00", $start + 1);
            } elseif (str_contains($fname, 'Theater Room')) {
                $data['start_time'] = '19:00:00';
                $data['end_time'] = '21:00:00';
                $data['description'] = 'Nobar: ' . $faker->sentence(2);
            } elseif (str_contains($fname, 'Co-Working Space') || str_contains($fname, 'cws')) {
                $data['start_time'] = '09:00:00';
                $data['end_time'] = '17:00:00';
                $data['jumlah_orang'] = rand(1, 10);
            } elseif (str_contains($fname, 'Serba Guna Hall') || str_contains($fname, 'serba guna')) {
                $data['start_time'] = '14:00:00';
                $data['end_time'] = '16:00:00';
            } else {
                $data['start_time'] = '08:00:00';
                $data['end_time'] = '09:00:00';
            }

            Booking::create($data);
        }

        $this->command->info("Selesai! 1000 data berhasil masuk.");
    }
}