<?php

namespace Database\Seeders;

use App\Models\ShippingSetting;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    public function run(): void
    {
        ShippingSetting::create([
            'tiers' => [
                ['max' => 5, 'normal' => 15.00, 'remote' => 25.00],
                ['max' => 10, 'normal' => 12.00, 'remote' => 20.00],
                ['max' => 20, 'normal' => 10.00, 'remote' => 18.00],
                ['max' => 50, 'normal' => 8.00, 'remote' => 15.00],
                ['max' => 999, 'normal' => 5.00, 'remote' => 12.00],
            ],
            'extra_per_kg' => 2.00,
            'fuel_percent' => 0.02,
            'packaging_fee' => 5.25,
            'epg_percent' => 0.10,
            'epg_min' => 2.00,
            'vat_percent' => 0.05,
            'volumetric_divisor' => 5000,
            'installation_fee' => 200.00,
        ]);
    }
}