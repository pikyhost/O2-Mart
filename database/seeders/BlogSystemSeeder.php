<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogSystemSeeder extends Seeder
{
    
    public function run(): void
    {
        // 1. Create an author
        $author = User::firstOrCreate(
            ['email' => 'blogger@example.com'],
            [
                'name' => 'Blog Author',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $tech = BlogCategory::firstOrCreate(
            ['name' => 'Tech'],
            [
                'description' => 'Technology and updates',
                'is_active' => true,
            ]
        );

        $tips = BlogCategory::firstOrCreate(
            ['name' => 'Tips'],
            [
                'description' => 'Maintenance and safety tips',
                'is_active' => true,
                'parent_id' => $tech->id,
            ]
        );


        // 3. Create tags
        $tags = collect(['Engine', 'Oil', 'Battery', 'Maintenance'])->map(function ($tagName) {
            return Tag::updateOrCreate(
                ['name' => $tagName],
                ['is_active' => true]
            );
        });


        // 4. Create blogs
        for ($i = 1; $i <= 5; $i++) {
            $blog = Blog::firstOrCreate(
                ['slug' => Str::slug("How to take care of your car #$i")],
                [
                    'title' => "How to take care of your car #$i",
                    'content' => "### Car Maintenance Guide\n\nThis is a sample content for blog post #$i.",
                    'blog_category_id' => $tips->id,
                    'author_id' => $author->id,
                    'is_active' => true,
                    'published_at' => now()->subDays($i),
                ]
            );


            // Attach tags
            $blog->tags()->attach($tags->random(2));

            // Add fake image
            try {
                $blog
                    ->addMediaFromUrl('https://via.placeholder.com/800x600.png?text=Blog+' . $i)
                    ->toMediaCollection('main_blog_image');
            } catch (\Exception $e) {
                logger()->warning("Failed to attach image to blog #$i: " . $e->getMessage());
            }

        }
    }
}
