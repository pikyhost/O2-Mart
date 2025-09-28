<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MobileVanService;
use App\Models\InstallerShop;
use App\Models\ProductType;
use App\Models\WorkingHour;
use App\Models\Day;

class InstallationServicesSeeder extends Seeder
{
    public function run(): void
    {
        // Create product types
        $productTypes = [
            'auto_part' => ProductType::firstOrCreate(['name' => 'auto_part']),
            'battery' => ProductType::firstOrCreate(['name' => 'battery']),
            'tyre' => ProductType::firstOrCreate(['name' => 'tyre']),
            'rim' => ProductType::firstOrCreate(['name' => 'rim']),
        ];

        // Create days if they don't exist
        $days = [
            1 => Day::firstOrCreate(['id' => 1], ['name' => 'Sunday']),
            2 => Day::firstOrCreate(['id' => 2], ['name' => 'Monday']),
            3 => Day::firstOrCreate(['id' => 3], ['name' => 'Tuesday']),
            4 => Day::firstOrCreate(['id' => 4], ['name' => 'Wednesday']),
            5 => Day::firstOrCreate(['id' => 5], ['name' => 'Thursday']),
            6 => Day::firstOrCreate(['id' => 6], ['name' => 'Friday']),
            7 => Day::firstOrCreate(['id' => 7], ['name' => 'Saturday']),
        ];

        // Create Mobile Van Services
        $mobileVans = [
            [
                'name' => 'Quick Tyre Mobile Service',
                'location' => 'Dubai Marina',
                'product_types' => ['tyre', 'rim']
            ],
            [
                'name' => 'Battery Express Van',
                'location' => 'Downtown Dubai',
                'product_types' => ['battery', 'auto_part']
            ],
            [
                'name' => 'All-in-One Mobile Service',
                'location' => 'Jumeirah',
                'product_types' => ['tyre', 'battery', 'auto_part', 'rim']
            ]
        ];

        foreach ($mobileVans as $vanData) {
            $van = MobileVanService::create([
                'name' => $vanData['name'],
                'location' => $vanData['location'],
                'is_active' => true
            ]);

            // Attach product types
            foreach ($vanData['product_types'] as $typeName) {
                $van->productTypes()->attach($productTypes[$typeName]->id);
            }

            // Create working hours (Mon-Fri: 9AM-6PM, Sat: 9AM-2PM, Sun: Closed)
            foreach ($days as $dayId => $day) {
                if ($dayId == 1) { // Sunday - Closed
                    WorkingHour::create([
                        'mobile_van_service_id' => $van->id,
                        'day_id' => $dayId,
                        'is_closed' => true
                    ]);
                } elseif ($dayId == 7) { // Saturday - Half day
                    WorkingHour::create([
                        'mobile_van_service_id' => $van->id,
                        'day_id' => $dayId,
                        'opening_time' => '09:00:00',
                        'closing_time' => '14:00:00',
                        'is_closed' => false
                    ]);
                } else { // Monday-Friday - Full day
                    WorkingHour::create([
                        'mobile_van_service_id' => $van->id,
                        'day_id' => $dayId,
                        'opening_time' => '09:00:00',
                        'closing_time' => '18:00:00',
                        'is_closed' => false
                    ]);
                }
            }
        }

        // Create Installer Shops
        $installerShops = [
            [
                'name' => 'Pro Tyre Installation Center',
                'location' => 'Al Quoz',
                'product_types' => ['tyre', 'rim']
            ],
            [
                'name' => 'Battery & Auto Parts Shop',
                'location' => 'Deira',
                'product_types' => ['battery', 'auto_part']
            ],
            [
                'name' => 'Complete Car Service Center',
                'location' => 'Sheikh Zayed Road',
                'product_types' => ['tyre', 'battery', 'auto_part', 'rim']
            ]
        ];

        foreach ($installerShops as $shopData) {
            $shop = InstallerShop::create([
                'name' => $shopData['name'],
                'location' => $shopData['location'],
                'is_active' => true
            ]);

            // Attach product types
            foreach ($shopData['product_types'] as $typeName) {
                $shop->productTypes()->attach($productTypes[$typeName]->id);
            }

            // Create working hours (Mon-Sat: 8AM-8PM, Sun: Closed)
            foreach ($days as $dayId => $day) {
                if ($dayId == 1) { // Sunday - Closed
                    WorkingHour::create([
                        'installer_shop_id' => $shop->id,
                        'day_id' => $dayId,
                        'is_closed' => true
                    ]);
                } else { // Monday-Saturday - Full day
                    WorkingHour::create([
                        'installer_shop_id' => $shop->id,
                        'day_id' => $dayId,
                        'opening_time' => '08:00:00',
                        'closing_time' => '20:00:00',
                        'is_closed' => false
                    ]);
                }
            }
        }
    }
}