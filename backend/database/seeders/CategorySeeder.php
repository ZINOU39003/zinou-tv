<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sports',
                'name_ar' => 'رياضة',
                'slug' => 'sports',
                'icon' => 'sports_soccer',
                'sort_order' => 1,
            ],
            [
                'name' => 'Movies',
                'name_ar' => 'أفلام',
                'slug' => 'movies',
                'icon' => 'movie',
                'sort_order' => 2,
            ],
            [
                'name' => 'News',
                'name_ar' => 'أخبار',
                'slug' => 'news',
                'icon' => 'newspaper',
                'sort_order' => 3,
            ],
            [
                'name' => 'Kids',
                'name_ar' => 'أطفال',
                'slug' => 'kids',
                'icon' => 'child_care',
                'sort_order' => 4,
            ],
            [
                'name' => 'Documentary',
                'name_ar' => 'وثائقيات',
                'slug' => 'documentary',
                'icon' => 'nature',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
