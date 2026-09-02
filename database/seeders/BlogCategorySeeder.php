<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogCategories = [
            ['name' => 'Property Stories', 'slug' => 'property-stories'],
            ['name' => 'Destination Insights', 'slug' => 'destination-insights'],
            ['name' => 'Industry Intelligence', 'slug' => 'industry-intelligence'],
            ['name' => 'New Portfolio Members', 'slug' => 'new-portfolio-members'],
            ['name' => 'Buyer Perspectives', 'slug' => 'buyer-perspectives'],
            ['name' => 'Representation News', 'slug' => 'representation-news'],
        ];

        foreach ($blogCategories as $blogCat) {
            DB::table('blog_categories')->updateOrInsert(
                ['slug' => $blogCat['slug']],
                [
                    'name' => $blogCat['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
