<?php

namespace Database\Seeders;

use App\Models\Tyre;
use App\Models\TyreBrand;
use App\Models\TyreAttribute;
use App\Models\TyreSize;
use App\Models\TyreModel;
use App\Models\TyreCountry;
use Illuminate\Database\Seeder;

class TyreSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'title' => 'Pirelli P 225/50R17',
                'description' => "O2Mart offers tyres That are engineered for durability, safety...",
                'brand' => 'Pirelli',
                'tyre_attribute' => '225/50R17',
                'tyre_size' => 'P 225/50R17',
                'width' => 225,
                'height' => 50,
                'wheel_diameter' => 17,
                'model' => 'CINT P7',
                'load_index' => '94',
                'speed_rating' => 'H',
                'weight_kg' => 10,
                'production_year' => 2023,
                'country_of_origin' => 'MEXICO',
                'warranty' => '1 Year',
                'price_vat_inclusive' => 469.43,
                'discount_percentage' => 0,
                'sku' => 'O2-T-SF-P-0001',
                'alt_text' => 'Pirelli_CINT P7',
                'image_url' => 'https://ik.imagekit.io/O2Mart/Tyres/Tyres_Images_Catalouge_V1.2/Pirelli_CINT%20P7.png?updatedAt=1736849011750',
            ],
        ];

        foreach ($data as $item) {
            $brand = TyreBrand::firstOrCreate(['name' => $item['brand']]);
            $tyreSize = TyreSize::firstOrCreate(['size' => $item['tyre_size']]);
            $model = TyreModel::firstOrCreate(['name' => $item['model']]);
            $attribute = TyreAttribute::firstOrCreate(['tyre_attribute' => $item['tyre_attribute']]);
            $country = TyreCountry::firstOrCreate(['name' => $item['country_of_origin']]);

            $discountedPrice = $item['price_vat_inclusive'];
            if ($item['discount_percentage'] > 0) {
                $discountedPrice = $item['price_vat_inclusive'] * (1 - $item['discount_percentage'] / 100);
            }

            $tyre = Tyre::updateOrCreate(
            ['sku' => $item['sku']],
            [
                'title' => $item['title'],
                'slug' => \Str::slug($item['title']),
                'description' => $item['description'],
                'tyre_brand_id' => $brand->id,
                'tyre_size_id' => $tyreSize->id,
                'tyre_model_id' => $model->id,
                'tyre_attribute_id' => $attribute->id,
                'tyre_country_id' => $country->id,
                'width' => $item['width'],
                'height' => $item['height'],
                'wheel_diameter' => $item['wheel_diameter'],
                'load_index' => $item['load_index'],
                'speed_rating' => $item['speed_rating'],
                'weight_kg' => $item['weight_kg'],
                'production_year' => $item['production_year'],
                'warranty' => $item['warranty'],
                'price_vat_inclusive' => $item['price_vat_inclusive'],
                'discount_percentage' => $item['discount_percentage'],
                'discounted_price' => $discountedPrice,
                'alt_text' => $item['alt_text'],
            ]
        );


            // Add feature image
            if (!empty($item['image_url'])) {
                $tyre->addMediaFromUrl($item['image_url'])->toMediaCollection('tyre_feature_image');
            }
        }
    }
}
