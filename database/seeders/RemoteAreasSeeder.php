<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Area;

class RemoteAreasSeeder extends Seeder
{
    public function run(): void
    {
        $remoteAreas = [
            ['city' => 'Abu Dhabi', 'name' => 'Ghayathi'],
            ['city' => 'Abu Dhabi', 'name' => 'Bad Al Matawa'],
            ['city' => 'Abu Dhabi', 'name' => 'Al Nadra'],
            ['city' => 'Abu Dhabi', 'name' => 'Al Hmara'],
            ['city' => 'Abu Dhabi', 'name' => 'BARAKA'],
            ['city' => 'Abu Dhabi', 'name' => 'AL SILA'],
            ['city' => 'Abu Dhabi', 'name' => 'Madinat Zayed'],
            ['city' => 'Abu Dhabi', 'name' => 'Habshan'],
            ['city' => 'Abu Dhabi', 'name' => 'Bainuona'],
            ['city' => 'Abu Dhabi', 'name' => 'LIWA'],
            ['city' => 'Abu Dhabi', 'name' => 'ASAB'],
            ['city' => 'Abu Dhabi', 'name' => 'HAMEEM'],
            ['city' => 'Abu Dhabi', 'name' => 'Ruwais'],
            ['city' => 'Abu Dhabi', 'name' => 'Mirfa'],
            ['city' => 'Abu Dhabi', 'name' => 'Abu Al Bayad'],
            ['city' => 'Abu Dhabi', 'name' => 'AL Hamra'],
            ['city' => 'Abu Dhabi', 'name' => 'Jabel Al Dhani'],
            ['city' => 'Dubai', 'name' => 'Hatta'],
            ['city' => 'Dubai', 'name' => 'Nazwa'],
            ['city' => 'Sharjah', 'name' => 'Lehbab'],
            ['city' => 'Sharjah', 'name' => 'Madam'],
            ['city' => 'Ajman', 'name' => 'Masfout'],
            ['city' => 'Ras Al Khaimah', 'name' => 'Wadi Al Shiji'],
            ['city' => 'Ras Al Khaimah', 'name' => 'Al showka'],
            ['city' => 'Al Ain', 'name' => 'Nahil'],
            ['city' => 'Al Ain', 'name' => 'Sawehan'],
            ['city' => 'Al Ain', 'name' => 'Al Dahra'],
            ['city' => 'Al Ain', 'name' => 'Al Qua'],
            ['city' => 'Al Ain', 'name' => 'Al Wagan'],
        ];

        foreach ($remoteAreas as $entry) {
            $city = City::firstOrCreate(
                ['name' => $entry['city']],
                ['governorate_id' => 1] 
            );

            Area::updateOrCreate(
                ['name' => $entry['name']],
                [
                    'city_id' => $city->id,
                    'is_remote' => true,
                ]
            );
        }
    }
}
