<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutUs::create([
            'intro_title' => 'Welcome to Our Company',
            'intro_text' => 'We are a passionate team dedicated to delivering innovative solutions that empower our clients to achieve their goals. Our mission is to create value through creativity and technology.',
            'intro_cta' => 'Learn More',
            'intro_url' => '/about',
            'center_title' => 'Our Vision',
            'center_text' => 'Our vision is to lead the industry with cutting-edge services and a commitment to sustainability. We strive to make a positive impact on the world.',
            'center_cta' => 'Send Inquiry',
            'center_url' => '/services',
            'latest_title' => 'What’s New',
            'latest_text' => 'Stay updated with our latest projects and initiatives. We’re constantly evolving to meet the needs of our community and clients.',
            'about_us_video_path' => 'about-us/intro-video.mp4',
            'center_image_path' => 'about-us/center-image.jpg',
            'latest_image_path' => 'about-us/latest-image.jpg',
        ]);
    }
}
