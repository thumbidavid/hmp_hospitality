<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only run seeders for which migrations have been created
        $this->call([
            RoleAndUserSeeder::class,
            CountrySeeder::class,
            PortfolioCategorySeeder::class,
            SettingSeeder::class,
            AmenitySeeder::class,
            BuyerTypeSeeder::class,
            AgencySupportServiceSeeder::class,
            BlogCategorySeeder::class,
        ]);
    }
}
