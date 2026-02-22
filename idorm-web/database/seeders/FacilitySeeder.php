<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\FacilityItem;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['name' => 'Dapur', 'type' => 'light'],
            ['name' => 'Mesin Cuci', 'type' => 'heavy'],
            ['name' => 'Theater Room', 'type' => 'light'],
            ['name' => 'Co-Working Space', 'type' => 'light'],
            ['name' => 'Serba Guna Hall', 'type' => 'light'],
        ];
        
        foreach ($facilities as $f) {
            // Gunakan firstOrCreate agar data tidak duplikat jika seeder dijalankan ulang
            Facility::firstOrCreate(['name' => $f['name']], $f);
        }

        // 2. Mapping Data Items
        $data = [
            'Dapur' => ['Kompor', 'Rice Cooker Kecil', 'Rice Cooker Besar', 'Airfryer Halal', 'Airfryer Non Halal'],
            
            'Mesin Cuci' => [
                'Mesin Cuci Male 1', 'Mesin Cuci Male 2', 'Mesin Cuci Male 3', 'Mesin Cuci Male 4', 'Mesin Cuci Male 5',
                'Mesin Cuci Female 1', 'Mesin Cuci Female 2', 'Mesin Cuci Female 3', 'Mesin Cuci Female 4', 'Mesin Cuci Female 5'
            ],
            
            'Theater Room' => ['Theater Room'],
            
            'Co-Working Space' => ['Co-Working Space'],
            
            'Serba Guna Hall' => ['Serba Guna A', 'Serba Guna B'],
        ];

        foreach ($data as $facilityName => $items) {
            // Pastikan kolom ini sama dengan yang di-insert di atas
            $facility = Facility::where('name', $facilityName)->first();

            if ($facility) {
                foreach ($items as $itemName) {
                    // Gunakan firstOrCreate juga untuk items agar aman dari duplikasi
                    FacilityItem::firstOrCreate([
                        'facility_id' => $facility->id,
                        'name'        => $itemName,
                    ]);
                }
            }
        }
    }
}
