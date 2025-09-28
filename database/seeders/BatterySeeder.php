<?php

namespace Database\Seeders;

use App\Models\Battery;
use App\Models\BatteryAttribute;
use App\Models\BatteryBrand;
use App\Models\BatteryCapacity;
use App\Models\BatteryDimension;
use App\Models\BatteryCountry;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cviebrock\EloquentSluggable\Services\SlugService;

class BatterySeeder extends Seeder
{
    public function run(): void
    {
        $batteries = [
            [
                'name' => 'Amaron - 85D23L 12V 60AH JIS Car Battery',
                'attribute' => 'Amaron - 85D23L 12V 60AH JIS',
                'capacity' => '60AH',
                'dimension' => '240x175x210',
                'weight' => 15,
                'brand' => 'AMARON',
                'item_code' => null,
                'sku' => 'AMARON-0056',
                'regular_price' => 850,
                'discounted_price' => 450,
                'discount_percent' => 47,
                'country' => 'India',
                'warranty' => '1 Year',
                'description' => 'Car batteries are a high quality, premium replacement for your original car battery. This range has up to 15% more "Cold Cranking" power than the original equivalent, and up to 20% longer service life.',
                'image_url' => 'https://i.postimg.cc/fb4G4QGh/bosch-battery-bosch-80d26r-right-terminal-12v-70ah-jis-car-battery-bosch-0054-40380963750105-1024x10.jpg',
            ],
            [
                'name' => 'Amaron - 55B24LS (NS60) 12V 45AH JIS Car Battery',
                'attribute' => 'Amaron - 55B24LS (NS60) 12V 45AH JIS',
                'capacity' => '45AH',
                'dimension' => '238x129x227', 
                'weight' => 13,
                'brand' => 'AMARON',
                'item_code' => null,
                'sku' => 'AMARON-006',
                'regular_price' => 550,
                'discounted_price' => 366,
                'discount_percent' => 33,
                'country' => 'India',
                'warranty' => '1 Year',
                'description' => 'Car batteries are ideal for modern vehicles which require a higher quality battery.',
                'image_url' => 'https://i.postimg.cc/xC0frtgf/varta-car-battery.webp',
            ],
            [
                'name' => 'Amaron - 110D26L Left Terminal 12V 80AH JIS Car Battery',
                'attribute' => 'Amaron - 110D26L Left Terminal 12V 80AH JIS',
                'capacity' => '80AH',
                'dimension' => '260x173x225',
                'weight' => 17.5,  
                'brand' => 'AMARON',
                'item_code' => null,
                'sku' => 'AMARON-0055',
                'regular_price' => 821,
                'discounted_price' => 524,
                'discount_percent' => 36,
                'country' => 'India',
                'warranty' => '1 Year',
                'description' => 'Up to 20% longer service life. All batteries are maintenance free.',
                'image_url' => 'https://i.postimg.cc/fb4G4QGh/bosch-battery-bosch-80d26r-right-terminal-12v-70ah-jis-car-battery-bosch-0054-40380963750105-1024x10.jpg',
            ],
        ];

        $category = Category::firstOrCreate(
            ['name' => 'Batteries'],
            ['slug' => SlugService::createSlug(Category::class, 'slug', 'Batteries'), 'is_active' => true]
        );

        foreach ($batteries as $data) {
            $brand = BatteryBrand::firstOrCreate(['value' => $data['brand']]);
            $capacity = $data['capacity'] ? BatteryCapacity::firstOrCreate(['value' => $data['capacity']]) : null;
            $dimension = $data['dimension'] ? BatteryDimension::firstOrCreate(['value' => $data['dimension']]) : null;
            $country = BatteryCountry::firstOrCreate(['name' => $data['country']]);

            $battery = Battery::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'battery_brand_id' => $brand->id,
                    'capacity_id' => $capacity?->id,
                    'dimension_id' => $dimension?->id,
                    'weight' => $data['weight'],
                    'item_code' => $data['item_code'],
                    'regular_price' => $data['regular_price'],
                    'discounted_price' => $data['discounted_price'],
                    'discount_percentage' => $data['discount_percent'],
                    'battery_country_id' => $country->id,
                    'warranty' => $data['warranty'],
                    'category_id' => $category->id,
                ]
            );

            // Battery Attribute
            if (!empty($data['attribute'])) {
                $attribute = BatteryAttribute::firstOrCreate([
                    'name' => $data['attribute'],
                    'car_make_id' => 1,
                    'car_model_id' => 1,
                    'model_year' => 2000,
                ]);
                $battery->attributes()->syncWithoutDetaching([$attribute->id]);
            }

            // Upload Image & save its URL to image_url column
            if (!empty($data['image_url'])) {
                try {
                    $battery->addMediaFromUrl($data['image_url'])
                        ->toMediaCollection('battery_secondary_image');

                    $battery->update([
                        'image_url' => $battery->getFirstMediaUrl('battery_secondary_image'),
                    ]);
                } catch (\Exception $e) {
                    dump("Failed to add image for {$battery->name}: " . $e->getMessage());
                }
            }
        }
    }
}
