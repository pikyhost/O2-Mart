<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();
        $category = BlogCategory::first();

        if (!$author || !$category) {
            $this->command->error('Please ensure you have at least one user and blog category before running this seeder.');
            return;
        }

        $blogs = [
            [
                'title' => 'Best Tire Maintenance Tips for 2024',
                'slug' => 'best-tire-maintenance-tips-2024',
                'content' => '# Tire Maintenance Guide

Proper tire maintenance is crucial for vehicle safety and performance. Here are the essential tips every driver should know:

## Regular Pressure Checks
Check your tire pressure monthly using a reliable gauge. Proper inflation improves fuel efficiency and extends tire life.

## Rotation Schedule
Rotate your tires every 5,000-8,000 miles to ensure even wear patterns and maximize their lifespan.',
                'meta_title' => 'Best Tire Maintenance Tips for 2024 | O2Mart Auto Guide',
                'meta_description' => 'Learn essential tire maintenance tips to extend tire life, improve safety, and save money. Expert advice on pressure checks, rotation, and more.',
                'alt_text' => 'Car tire being checked for proper maintenance and pressure',
                'is_active' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'How to Choose the Right Car Battery',
                'slug' => 'how-to-choose-right-car-battery',
                'content' => '# Car Battery Selection Guide

Choosing the right car battery is essential for reliable vehicle performance. This comprehensive guide will help you make the best choice.

## Battery Types
- **Lead-Acid**: Traditional and affordable
- **AGM**: Advanced performance and durability
- **Lithium**: Lightweight and long-lasting

## Key Factors
Consider your climate, driving habits, and vehicle requirements when selecting a battery.',
                'meta_title' => 'How to Choose the Right Car Battery | Complete Guide 2024',
                'meta_description' => 'Complete guide to choosing the perfect car battery. Compare types, sizes, and features to find the best battery for your vehicle needs.',
                'alt_text' => 'Various car batteries displayed showing different types and sizes',
                'is_active' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Top 5 Alloy Rim Styles for Modern Cars',
                'slug' => 'top-5-alloy-rim-styles-modern-cars',
                'content' => '# Modern Alloy Rim Styles

Upgrade your vehicle\'s appearance and performance with these trending alloy rim styles that combine aesthetics with functionality.

## 1. Multi-Spoke Design
Classic elegance with enhanced brake cooling and lightweight construction.

## 2. Deep Dish Rims
Bold statement pieces that add aggressive styling to sports cars.

## 3. Mesh Pattern
Timeless design offering excellent strength-to-weight ratio.',
                'meta_title' => 'Top 5 Alloy Rim Styles for Modern Cars | O2Mart Style Guide',
                'meta_description' => 'Discover the hottest alloy rim styles for 2024. From multi-spoke to deep dish designs, find the perfect rims to enhance your car\'s look.',
                'alt_text' => 'Collection of modern alloy rims showing different stylish designs',
                'is_active' => true,
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::create([
                ...$blogData,
                'blog_category_id' => $category->id,
                'author_id' => $author->id,
            ]);
        }

        $this->command->info('3 blogs with SEO fields created successfully!');
    }
}