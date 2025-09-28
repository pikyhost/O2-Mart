<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSectionSeeder extends Seeder
{
    public function run(): void
    {

        $sections = [
            [
                'type' => 'auto_part',
                'background_image' => null,
                'section1_title' => 'Why Choose Our Auto Parts?',
                'section1_text1' => '<p>All our auto parts are 100% original and compatible with your vehicle.</p>',
                'section1_text2' => '<p>We offer warranty-backed components from trusted brands.</p>',
                'section2_title' => 'Delivery & Support',
                'section2_text' => '<p>Fast delivery across UAE and expert support on all orders.</p>',
            ],
            [
                'type' => 'tyre',
                'background_image' => null,
                'section1_title' => 'Best Tyres for UAE Roads',
                'section1_text1' => '<p>Explore top tyre brands like Michelin, Pirelli, and more.</p>',
                'section1_text2' => '<p>Available in all sizes with precise filters for your car.</p>',
                'section2_title' => 'Installation Options',
                'section2_text' => '<p>Mobile van or certified installer centers at your convenience.</p>',
            ],
            [
                'type' => 'battery',
                'background_image' => null,
                'section1_title' => 'Reliable Car Batteries',
                'section1_text1' => '<p>Long-life batteries built for the Middle East climate.</p>',
                'section1_text2' => '<p>From top brands like AC Delco, Exide, Bosch, and more.</p>',
                'section2_title' => 'Warranty & Replacement',
                'section2_text' => '<p>Get hassle-free warranty and free battery testing services.</p>',
            ],
            [
                'type' => 'rim',
                'background_image' => null,
                'section1_title' => 'Stylish & Durable Rims',
                'section1_text1' => '<p>Enhance the look and performance of your vehicle.</p>',
                'section1_text2' => '<p>Choose from a wide variety of designs and bolt patterns.</p>',
                'section2_title' => 'Professional Fitment',
                'section2_text' => '<p>Ensure perfect fitment with expert installer network.</p>',
            ],
        ];

        foreach ($sections as $section) {
            DB::table('product_sections')->updateOrInsert(
                ['type' => $section['type']],
                $section
            );
        }
    }
}
