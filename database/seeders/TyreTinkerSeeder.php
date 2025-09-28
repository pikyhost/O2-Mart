<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\TyreAttribute;
use App\Models\TyreBrand;
use App\Models\TyreModel;
use App\Models\TyreSize;
use App\Models\Tyre;
use Illuminate\Support\Str;

class TyreTinkerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Add Make + Model
        $make = CarMake::firstOrCreate([
            'name' => 'HONDA',
            'slug' => Str::slug('HONDA'),
        ]);

        $model = CarModel::firstOrCreate([
            'name' => 'Civic',
            'slug' => Str::slug('Civic'),
            'car_make_id' => $make->id,
        ]);

        // 2. Create Tyre Attribute
        $attribute = TyreAttribute::create([
            'car_make_id' => $make->id,
            'car_model_id' => $model->id,
            'model_year' => 2023,
            'trim' => 'EX-T',
            'tyre_attribute' => '195/65R15',
            'tyre_oem' => 'OEM-HONDA-CIVIC',
        ]);

        // 3. Tyre Related Entities
        $brand = TyreBrand::firstOrCreate(['name' => 'Bridgestone']);
        $tyreModel = TyreModel::firstOrCreate(['name' => 'Turanza']);
        $size = TyreSize::firstOrCreate(['size' => '195/65R15']);

        // 4. Create Tyre
        Tyre::create([
            'title' => 'Bridgestone Turanza 195/65R15',
            'sku' => 'O2-T-BR-TUR-001',
            'width' => '195',
            'height' => '65',
            'wheel_diameter' => '15.00',
            'load_index' => '91',
            'speed_rating' => 'T',
            'production_year' => 2023,
            'price_vat_inclusive' => 320.00,
            'warranty' => '1 Year',
            'tyre_attribute_id' => $attribute->id,
            'tyre_brand_id' => $brand->id,
            'tyre_model_id' => $tyreModel->id,
            'tyre_size_id' => $size->id,
        ]);

        $this->command->info('✅ Tyre with HONDA CIVIC 2023 inserted successfully.');
    }
}
