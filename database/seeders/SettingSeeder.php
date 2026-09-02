<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['name' => 'City & Business', 'slug' => 'city-business'],
            ['name' => 'Beach & Leisure', 'slug' => 'beach-leisure'],
            ['name' => 'Safari & Wildlife', 'slug' => 'safari-wildlife'],
            ['name' => 'Coastal & Leisure', 'slug' => 'coastal-leisure'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['slug' => $setting['slug']],
                [
                    'name' => $setting['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
