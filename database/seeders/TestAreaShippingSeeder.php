<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use App\Models\AutoPart;
use App\Models\Category;

class TestAreaShippingSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing country or create with required fields
        $country = Country::first() ?? Country::create([
            'name' => 'Test Country',
            'code' => 'TC',
            'phone_code' => '+999'
        ]);
        
        $governorate = Governorate::firstOrCreate([
            'name' => 'Test Governorate',
            'country_id' => $country->id
        ]);
        
        $city = City::firstOrCreate([
            'name' => 'Test City',
            'governorate_id' => $governorate->id
        ]);
        
        // Create areas with different shipping costs
        $area1 = Area::firstOrCreate([
            'name' => 'Normal Area',
            'city_id' => $city->id
        ], [
            'shipping_cost' => 0,
            'is_active' => true
        ]);
        
        $area2 = Area::firstOrCreate([
            'name' => 'Expensive Area',
            'city_id' => $city->id
        ], [
            'shipping_cost' => 25,
            'is_active' => true
        ]);
        
        // Create test category and auto part
        $category = Category::firstOrCreate([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true
        ]);
        
        $autoPart = AutoPart::firstOrCreate([
            'sku' => 'TEST-001',
            'name' => 'Test Auto Part',
            'slug' => 'test-auto-part'
        ], [
            'price_including_vat' => 50,
            'discounted_price' => 50,
            'weight' => 1,
            'category_id' => $category->id,
            'photo_link' => 'test.jpg'
        ]);
        
        echo "Test data created:\n";
        echo "Area 1 (Normal): ID={$area1->id}, Cost={$area1->shipping_cost}\n";
        echo "Area 2 (Expensive): ID={$area2->id}, Cost={$area2->shipping_cost}\n";
        echo "AutoPart: ID={$autoPart->id}, Price={$autoPart->price_including_vat}\n";
    }
}