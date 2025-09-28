<?php

namespace Database\Seeders;

use App\Models\AutoPart;
use App\Models\AutoPartBrand;
use App\Models\AutoPartCountry;
use App\Models\Category;
use App\Models\ViscosityGrade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Cviebrock\EloquentSluggable\Services\SlugService;

class AutoPartSeeder extends Seeder
{
    public function run(): void
    {
        $autoParts = [
            [
                'category_name' => 'Oil & Lubes',
                'sub_category_name' => 'Engine Oil',
                'name' => 'Engine Oil Total Quartz 7000  10W-40',
                'weight' => 4,
                'height' => 24.2,
                'width' => 18.2,
                'length' => 10.9,
                'details' => 'Engine oil is essential for protecting your vehicle’s engine...',
                'viscosity_grade' => '10W-40',
                'brand' => 'Total',
                'country' => 'UAE',
                'sku' => 'O2-Total-SL-00001',
                'price_including_vat' => 74,
                'discount_percentage' => null,
                'discounted_price' => null,
                'description' => 'Designed to meet the demands of modern engines...',
                'photo_alt_text' => 'Engine Oil Total Quartz 7000  10W_40.png',
                'image_url' => 'https://ik.imagekit.io/O2Mart/Ghr/Fluids-Lubes/Oil_and_Battaries_Images/Engine%20Oil%20Total%20Quartz%207000%20%2010W_40.png?updatedAt=1739961976417',
            ],
        ];

        foreach ($autoParts as $data) {
            $brand = AutoPartBrand::firstOrCreate(['name' => $data['brand']]);
            $country = AutoPartCountry::firstOrCreate(['name' => $data['country']]);
            $viscosity = ViscosityGrade::firstOrCreate(['name' => $data['viscosity_grade']]);
            $parentCategory = Category  ::firstOrCreate(
                ['name' => $data['category_name']],
                ['slug' => SlugService::createSlug(Category::class, 'slug', $data['category_name']), 'is_published' => true]
            );
            $subCategory = Category::firstOrCreate(
                ['name' => $data['sub_category_name'], 'parent_id' => $parentCategory->id],
                ['slug' => SlugService::createSlug(Category::class, 'slug', $data['sub_category_name']), 'is_published' => true]
            );
            $part = AutoPart::firstOrNew(['sku' => $data['sku']]);

            $part = AutoPart::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'price_including_vat' => $data['price_including_vat'],
                    'discount_percentage' => $data['discount_percentage'],
                    'discounted_price' => $data['discounted_price'],
                    'description' => $data['description'],
                    'details' => $data['details'],
                    'photo_alt_text' => $data['photo_alt_text'],
                    'weight' => $data['weight'],
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'length' => $data['length'],
                    'auto_part_brand_id' => $brand->id,
                    'auto_part_country_id' => $country->id,
                    'viscosity_grade_id' => $viscosity->id,
                    'category_id' => $subCategory->id,
                ]
            );




            if (!empty($data['image_url'])) {
                try {
                    $part->addMediaFromUrl($data['image_url'])
                        ->toMediaCollection('auto_part_secondary_image');

                    $part->photo_link = $part->getAutoPartSecondaryImageUrl();
                    $part->save();
                } catch (\Exception $e) {
                    dump("⚠️ Failed to upload image for {$part->name}: " . $e->getMessage());
                }
            }



        }
    }
}
