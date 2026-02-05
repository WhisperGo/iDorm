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
        $faker = Faker::create('id_ID'); // Pakai locale Indonesia biar lebih pas

        // 1. Ambil data pendukung
        $residents = User::whereHas('role', fn($q) =>
                            $q->where('role_name',
                            ['Resident', 'Admin']))->get();
        $statuses = BookingStatus::all();
        $slots = TimeSlot::all();
        $facilities = Facility::all();

        if ($residents->isEmpty() || $facilities->isEmpty()) {
            $this->command->warn("Eits! Pastikan tabel Users (Resident) dan Facilities sudah ada datanya dulu ya.");
            return;
        }

        $this->command->info("Sedang mengisi 100 data booking dummy... Mohon tunggu sebentar.");

        // 2. Looping 1000 kali (Atau ganti angka 100 sesuai kebutuhan)
        for ($i = 0; $i < 1000; $i++) {
            // A. Ambil Resident secara acak dulu untuk tahu gendernya
            $resident = $residents->random();
            $gender = $resident->residentDetails->gender; // 'Male' atau 'Female'
            
            // B. Cari fasilitas yang cocok:
            // Jika Mesin Cuci, cari yang namanya mengandung gender si resident.
            // Jika bukan Mesin Cuci, ambil bebas (random).
            $filteredFacilities = $facilities->filter(function($f) use ($gender) {
                if (str_contains(strtolower($f->name), 'mesin cuci')) {
                    return str_contains($f->name, $gender);
                }
                return true;
            });

            $facility = $filteredFacilities->values()->random();
            if ($facility instanceof \Illuminate\Support\Collection) {
                $this->command->error("Waduh! Variabel facility masih berupa Collection.");
                continue;
                };

            $fname = strtolower($facility->name);
            
            // Pilih tanggal acak antara 1 bulan lalu sampai 1 bulan depan
            $randomDate = $faker->dateTimeBetween('-1 month', '+1 month');
            $date = Carbon::instance($randomDate);
            $dateStr = $date->format('Y-m-d');

            // Penyesuaian Status ID sesuai DatabaseSeeder
            if ($date->isPast()) {
                $statusId = rand(0, 1) ? 5 : 3; // 5: Completed, 3: Canceled
                $cleanStatus = ($statusId == 5) ? 'approved' : 'pending';
            } elseif ($date->isToday()) {
                $statusId = rand(1, 4); // Booked, Accepted, Canceled, Verifying
                $cleanStatus = ($statusId == 4) ? 'pending' : 'pending';
            } else {
                $statusId = 1; // 1: Booked
                $cleanStatus = 'pending';
            }

            // --- LOGIKA AMBIL ITEM (Dinamis & Efisien) ---
            $itemsQuery = FacilityItem::where('facility_id', $facility->id);

            // Jika Mesin Cuci, filter item sesuai gender penghuni
            if (str_contains($fname, 'mesin cuci')) {
                $itemsQuery->where('name', 'like', "%{$gender}%");
            }

            $facilityItem = $itemsQuery->inRandomOrder()->first();

            // Proteksi: Jika fasilitas tidak punya item, lewati loop ini
            if (!$facilityItem) {
                continue;
            }

            $data = [
                'user_id'       => $resident->id,
                'facility_id'   => $facility->id,
                'booking_date'  => $dateStr,
                'status_id'     => $statusId,
                'cleanliness_status' => $cleanStatus,
                'created_at'    => $date,
                'updated_at'    => $date,
            ];

            // 3. Variasi Data Per Kategori (Custom Data)
            if (str_contains($fname, 'mesin cuci')) {
                $slot = $slots->random();
                $data['slot_id']    = $slot->id;
                $data['start_time'] = $slot->start_time;
                $data['end_time']   = $slot->end_time;
            }
            elseif (str_contains(strtolower($fname), 'dapur')) {
                $start = rand(10, 21);
                $data['start_time'] = sprintf("%02d:00:00", $start);
                $data['end_time']   = sprintf("%02d:30:00", $start + 1);

                // LOGIKA DINAMIS: Ambil ID dari tabel facility_items
                $facility = \App\Models\Facility::where('name', 'Dapur')->first();

                if ($facility) {
                    // Ambil satu ID secara acak dari item yang terhubung dengan Dapur
                    $data['facility_item_id'] = \App\Models\FacilityItem::where('facility_id', $facility->id)
                        ->pluck('id')
                        ->random();
                }
            }
            elseif (str_contains($fname, 'theater')) {
                $data['start_time'] = '19:00:00';
                $data['end_time']   = '21:00:00';
                $data['description']= $faker->randomElement(['Nonton Bareng ' . $faker->sentence(2), 'Main PS Bareng', 'Nobar Timnas']);
            }
            elseif (str_contains($fname, 'cws') || str_contains($fname, 'working')) {
                $data['start_time'] = '09:00:00';
                $data['end_time']   = '17:00:00';
                $data['jumlah_orang'] = rand(1, 10);
            }
            elseif (str_contains(strtolower($fname), 'sergun') || str_contains(strtolower($fname), 'serba guna')) {
                $data['start_time'] = '14:00:00';
                $data['end_time']   = '16:00:00';

                // Cari fasilitas Serba Guna Hall secara dinamis
                $facility = \App\Models\Facility::where('name', 'Serba Guna Hall')->first();

                if ($facility) {
                    // Ambil ID acak dari items (Serba Guna A / B) yang terhubung
                    $data['facility_item_id'] = \App\Models\FacilityItem::where('facility_id', $facility->id)
                        ->pluck('id')
                        ->random();
                }
            }

            Booking::create($data);
        }

        $this->command->info("Mantap! 100 data booking dummy berhasil dibuat.");
    }
}