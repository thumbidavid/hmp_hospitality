<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortfolioCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $portfolioCategories = [
            [
                'name' => 'Hotels, Resorts & Lodges',
                'slug' => 'hotels-resorts-lodges',
                'singular_label' => 'Property',
                'sort_order' => 1,
            ],
            [
                'name' => 'Conference & Unique Venues',
                'slug' => 'conference-unique-venues',
                'singular_label' => 'Venue',
                'sort_order' => 2,
            ],
            [
                'name' => 'Serviced Residences',
                'slug' => 'serviced-residences',
                'singular_label' => 'Residence',
                'sort_order' => 3,
            ],
        ];

        foreach ($portfolioCategories as $cat) {
            DB::table('portfolio_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'singular_label' => $cat['singular_label'],
                    'sort_order' => $cat['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
