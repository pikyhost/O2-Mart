<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'United Arab Emirates',
                'code' => 'AE',
            ],
            [
                'name' => 'Kuwait',
                'code' => 'KW',
            ],
            [
                'name' => 'Saudi Arabia',
                'code' => 'SA',
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
