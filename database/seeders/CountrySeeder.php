<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Kenya', 'iso2_code' => 'KE', 'region' => 'East Africa'],
            ['name' => 'Tanzania', 'iso2_code' => 'TZ', 'region' => 'East Africa'],
            ['name' => 'Rwanda', 'iso2_code' => 'RW', 'region' => 'East Africa'],
            ['name' => 'South Africa', 'iso2_code' => 'ZA', 'region' => 'Southern Africa'],
            ['name' => 'Morocco', 'iso2_code' => 'MA', 'region' => 'North Africa'],
            ['name' => 'Egypt', 'iso2_code' => 'EG', 'region' => 'North Africa'],
            ['name' => 'Ghana', 'iso2_code' => 'GH', 'region' => 'West Africa'],
            ['name' => 'Nigeria', 'iso2_code' => 'NG', 'region' => 'West Africa'],
            ['name' => 'Zambia', 'iso2_code' => 'ZM', 'region' => 'Southern Africa'],
            ['name' => 'Senegal', 'iso2_code' => 'SN', 'region' => 'West Africa'],
            ['name' => 'Botswana', 'iso2_code' => 'BW', 'region' => 'Southern Africa'],
            ['name' => 'United Kingdom', 'iso2_code' => 'GB', 'region' => 'Europe'],
            ['name' => 'United States', 'iso2_code' => 'US', 'region' => 'North America'],
            ['name' => 'Germany', 'iso2_code' => 'DE', 'region' => 'Europe'],
            ['name' => 'UAE', 'iso2_code' => 'AE', 'region' => 'Middle East'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso2_code' => $country['iso2_code']],
                [
                    'name' => $country['name'],
                    'region' => $country['region'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
