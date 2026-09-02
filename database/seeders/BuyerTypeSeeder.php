<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuyerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyerTypes = [
            ['name' => 'Travel Advisor', 'slug' => 'travel-advisor'],
            ['name' => 'Tour Operator', 'slug' => 'tour-operator'],
            ['name' => 'Travel Management Company', 'slug' => 'travel-management-company'],
            ['name' => 'Corporate Travel Buyer', 'slug' => 'corporate-travel-buyer'],
            ['name' => 'Meeting & Event Planner', 'slug' => 'meeting-event-planner'],
            ['name' => 'Association', 'slug' => 'association'],
            ['name' => 'NGO', 'slug' => 'ngo'],
            ['name' => 'Government', 'slug' => 'government'],
            ['name' => 'Conference Organiser', 'slug' => 'conference-organiser'],
            ['name' => 'International Agency/Consortia', 'slug' => 'international-agency-consortia'],
            ['name' => 'Other', 'slug' => 'other'],
        ];

        foreach ($buyerTypes as $type) {
            DB::table('buyer_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
