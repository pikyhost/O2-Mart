<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['battery', 'tyre', 'rim', 'auto_part'];

        foreach ($types as $type) {
            ProductType::firstOrCreate(['name' => $type]);
        }
    }
}

