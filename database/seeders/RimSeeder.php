<?php


namespace Database\Seeders;

use App\Models\Category;
use App\Models\Rim;
use App\Models\RimBrand;
use App\Models\RimCountry;
use App\Models\RimSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Cviebrock\EloquentSluggable\Services\SlugService;

class RimSeeder extends Seeder
{
    public function run(): void
    {
        $rimBrand = RimBrand::firstOrCreate(['name' => 'ProWheels']);
        $rimCountry = RimCountry::firstOrCreate(['name' => 'Germany']);
        $rimSize = RimSize::firstOrCreate(['size' => '19" x 8.5J Front & 19" x 9.5 Rear']);
        $category = Category::firstOrCreate(
            ['name' => 'Rims'],
            ['slug' => Str::slug('Rims')]
        );
        $rim = Rim::create([
            'name' => '19" Wheel Nation Multi-Spoke Concave Wheels',
            'slug' => SlugService::createSlug(Rim::class, 'slug', '19 Wheel Nation Multi-Spoke Concave Wheels'),
            'description' => 'O2Mart- ProWheels-  5x114.3 / 19 x 8.5J Front & 19 x 9.5 Rear / Offsets +35',
            'colour' => 'Silver / Machined',
            'condition' => 'New',
            'specification' => 'PCD/Bolt Pattern 5x114.3 / 19 x 8.5J Front & 19 x 9.5 Rear / Offsets +35',
            'bolt_pattern' => '5x114.3',
            'offsets' => '(+35)',
            'centre_caps' => 'Provided with WN centre caps',
            'is_set_of_4' => true,
            'sku' => 'O2-RIM-S1-PW-001',
            'warranty' => 'Yes',
            'weight' => 40,
            'regular_price' => 2680,
            'discounted_price' => 2680,
            'alt_text' => 'O2Mart- ProWheels-  5x114.3 / 19 x 8.5J Front & 19 x 9.5 Rear / Offsets +35',
            'rim_brand_id' => $rimBrand->id,
            'rim_country_id' => $rimCountry->id,
            'rim_size_id' => $rimSize->id,
            'category_id' => $category->id,
        ]);
        $rimAttribute = \App\Models\RimAttribute::firstOrCreate([
            'car_make_id' => 1,
            'car_model_id' => 1,
            'model_year' => 2023,
            'name' => 'Bolt Pattern 5x114.3 / 19 x 8.5J Front & 19 x 9.5 Rear / Offsets +35',
        ]);

$rim->attributes()->attach($rimAttribute->id);

        $rim->addMediaFromUrl('https://ik.imagekit.io/O2Mart/Rims/prowheels-stealth-6-concave-wheel-6lug-polished-21x9-500_4820.png?updatedAt=1747306496812')
            ->toMediaCollection('rim_feature_image');
    }
}
