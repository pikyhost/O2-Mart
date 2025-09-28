<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopPage;

class ShopPageSeeder extends Seeder
{
    public function run(): void
    {
        ShopPage::truncate(); 

        ShopPage::create([
            'section_1_title' => 'Welcome to Our Shop',
            'section_1_content' => '<p>Discover a wide range of high-quality auto parts and accessories tailored for your vehicle.</p>',
            'section_2_title' => 'Why Shop With Us?',
            'section_2_content' => '<ul><li>Trusted by thousands</li><li>Fast shipping across UAE</li><li>Expert customer support</li></ul>',
        ]);
    }
}

