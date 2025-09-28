<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class JeeblyCitySeeder extends Seeder
{
    public function run(): void
    {
        $jeeblySupportedCities = [
            'Abu Dhabi'      => 'Abu Dhabi',
            'Ajman'          => 'Ajman',
            'Al Ain'         => 'Al-Ain',
            'Dubai'          => 'Dubai',
            'Fujairah'       => 'Fujairah',
            'Ras Al Khaimah' => 'Ras Al Khaimah',
            'Sharjah'        => 'Sharjah',
            'Umm Al-Quwain'  => 'Umm Al-Quwain',
        ];

        City::query()->update([
            'is_supported_by_jeebly' => false,
            'jeebly_name' => null,
        ]);

        foreach ($jeeblySupportedCities as $name => $jeeblyName) {
            City::where('name', $name)->update([
                'is_supported_by_jeebly' => true,
                'jeebly_name' => $jeeblyName,
            ]);
        }
    }
}
