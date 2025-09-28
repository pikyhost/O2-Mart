<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeSection;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        HomeSection::create([
            'section_1_title' => 'Auto Parts',
            'section_1_text' => '<p>We scan the market, compare prices, and recommend the best deals.</p>',
            'section_1_image' => 'home-sections/bg1.jpg',

            'section_2_title' => 'Your One-Stop Shop For Auto Parts In the UAE',
            'section_2_text' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
            'section_2_image' => 'home-sections/section2.jpg',

            'section_3_title' => 'How this work',
            'section_3_text' => '<p>Step-by-step explanation of the process.</p>',
            'section_3_image' => 'home-sections/section3.jpg',

            'section_4_image' => 'home-sections/section4.jpg',
            'section_4_link' => '/categories',
        ]);
    }
}
