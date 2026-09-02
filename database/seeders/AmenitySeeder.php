<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Spa', 'slug' => 'spa'],
            ['name' => 'Swimming Pool', 'slug' => 'swimming-pool'],
            ['name' => 'Gym', 'slug' => 'gym'],
            ['name' => 'Conference Facilities', 'slug' => 'conference-facilities'],
            ['name' => 'High-speed Wi-Fi', 'slug' => 'high-speed-wifi'],
            ['name' => 'Helipad', 'slug' => 'helipad'],
            ['name' => 'Airport Shuttle', 'slug' => 'airport-shuttle'],
            ['name' => 'Restaurant', 'slug' => 'restaurant'],
            ['name' => 'Bar', 'slug' => 'bar'],
        ];

        foreach ($amenities as $amenity) {
            DB::table('amenities')->updateOrInsert(
                ['slug' => $amenity['slug']],
                [
                    'name' => $amenity['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
