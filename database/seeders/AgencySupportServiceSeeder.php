<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencySupportServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencyServices = [
            ['name' => 'Air travel and business travel', 'slug' => 'air-travel-business-travel', 'sort_order' => 1],
            ['name' => 'Airport coordination', 'slug' => 'airport-coordination', 'sort_order' => 2],
            ['name' => 'Ground transportation', 'slug' => 'ground-transportation', 'sort_order' => 3],
            ['name' => 'Destination management', 'slug' => 'destination-management', 'sort_order' => 4],
            ['name' => 'Meetings and event management', 'slug' => 'meetings-event-management', 'sort_order' => 5],
            ['name' => 'Delegate registration and logistics', 'slug' => 'delegate-registration-logistics', 'sort_order' => 6],
            ['name' => 'Event production', 'slug' => 'event-production', 'sort_order' => 7],
            ['name' => 'Tours and experiences', 'slug' => 'tours-experiences', 'sort_order' => 8],
            ['name' => 'VIP and protocol services', 'slug' => 'vip-protocol-services', 'sort_order' => 9],
        ];

        foreach ($agencyServices as $service) {
            DB::table('agency_support_services')->updateOrInsert(
                ['slug' => $service['slug']],
                [
                    'name' => $service['name'],
                    'sort_order' => $service['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
