<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create Parent Categories
        $parentCategories = [
            'Auto Parts' => null,
            'Batteries' => null,
            'Tyres' => null,
            'Rims' => null,
        ];

        foreach ($parentCategories as $name => $id) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'meta_title' => $name,
                'meta_description' => 'Explore our collection of ' . strtolower($name),
                'is_published' => true,
                'parent_id' => null,
            ]);

            $parentCategories[$name] = $category->id; // store the ID
        }

        // Step 2: Create Subcategories
        $subcategories = [
            [
                'name' => 'Engine Components',
                'parent' => 'Auto Parts',
                'meta_description' => 'Essential engine parts and components',
            ],
            [
                'name' => 'Rechargeable Car Batteries',
                'parent' => 'Batteries',
                'meta_description' => 'Long-lasting and reliable car batteries',
            ],
            [
                'name' => 'All-Season Tyres',
                'parent' => 'Tyres',
                'meta_description' => 'Versatile tyres for all driving conditions',
            ],
            [
                'name' => 'Alloy Rims',
                'parent' => 'Rims',
                'meta_description' => 'Stylish alloy rims for better performance',
            ],
        ];

        foreach ($subcategories as $sub) {
            Category::create([
                'name' => $sub['name'],
                'slug' => Str::slug($sub['name']),
                'meta_title' => $sub['name'],
                'meta_description' => $sub['meta_description'],
                'is_published' => true,
                'parent_id' => $parentCategories[$sub['parent']],
            ]);
        }
    }
}
