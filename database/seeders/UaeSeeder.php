<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UaeSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues during seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables to ensure a clean slate
        DB::table('areas')->truncate();
        DB::table('cities')->truncate();
        DB::table('governorates')->truncate();
        DB::table('countries')->truncate();

        // Seed Country: United Arab Emirates
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'United Arab Emirates',
            'code' => 'AE',
            'shipping_cost' => 50,
            'shipping_estimate_time' => '2-5',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Governorates (Emirates)
        $governorates = [
            ['name' => 'Abu Dhabi', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
            ['name' => 'Dubai', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
            ['name' => 'Sharjah', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
            ['name' => 'Ajman', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
            ['name' => 'Umm Al Quwain', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
            ['name' => 'Ras Al Khaimah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
            ['name' => 'Fujairah', 'shipping_cost' => 50, 'shipping_estimate_time' => '2-5'],
        ];

        $governorateIds = [];
        foreach ($governorates as $governorate) {
            $governorateIds[$governorate['name']] = DB::table('governorates')->insertGetId([
                'name' => $governorate['name'],
                'country_id' => $countryId,
                'shipping_cost' => $governorate['shipping_cost'],
                'shipping_estimate_time' => $governorate['shipping_estimate_time'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed Cities and Areas
        $cities = [
            'Abu Dhabi' => [
                [
                    'name' => 'Abu Dhabi City',
                    'shipping_cost' => 30,
                    'shipping_estimate_time' => '1-3',
                    'areas' => [
                        ['name' => 'Al Bateen', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Khalidiyah', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Mushrif', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Reem Island', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Saadiyat Island', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Yas Island', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Khalifa City', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Madinat Zayed', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Nahyan', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Manhal', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Muroor', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Raha Beach', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Mussafah', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Mohammed Bin Zayed City', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Maqtaa', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Zaab', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Falah', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Shamkha', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Raha Gardens', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Maryah Island', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Rawdah', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Bahia', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Shahama', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Hydra Village', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Reef', 'shipping_cost' => 35, 'shipping_estimate_time' => '1-3'],
                    ],
                ],
                [
                    'name' => 'Al Ain',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Jimi', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Muwaiji', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Mutawaa', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Towayya', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Yahar', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Hili', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Khabisi', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Maqam', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Zakher', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Sarooj', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Foah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Buraimi', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Qou’a', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Falaj Hazza', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Agabiyya', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Mu’tarid', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Ain Oasis', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Markhaniya', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
                [
                    'name' => 'Al Dhafra',
                    'shipping_cost' => 60,
                    'shipping_estimate_time' => '3-5',
                    'areas' => [
                        ['name' => 'Madinat Zayed', 'shipping_cost' => 55, 'shipping_estimate_time' => '3-4'],
                        ['name' => 'Ghayathi', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Liwa', 'shipping_cost' => 65, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Ruwais', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Tarif', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Sila', 'shipping_cost' => 65, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Habshan', 'shipping_cost' => 65, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Al Mirfa', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Bida Zayed', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Al Sila’a', 'shipping_cost' => 65, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Al Marfa', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                    ],
                ],
                [
                    'name' => 'Bani Yas',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Bani Yas East', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Bani Yas West', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Shawamekh', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Mafraq', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Shakhbout City', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Al Gharbia',
                    'shipping_cost' => 55,
                    'shipping_estimate_time' => '3-5',
                    'areas' => [
                        ['name' => 'Madinat Zayed', 'shipping_cost' => 50, 'shipping_estimate_time' => '3-4'],
                        ['name' => 'Ghayathi', 'shipping_cost' => 55, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Liwa Oasis', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Mirfa', 'shipping_cost' => 55, 'shipping_estimate_time' => '3-5'],
                        ['name' => 'Sila', 'shipping_cost' => 60, 'shipping_estimate_time' => '3-5'],
                    ],
                ],
            ],
            'Dubai' => [
                [
                    'name' => 'Dubai City',
                    'shipping_cost' => 25,
                    'shipping_estimate_time' => '1-2',
                    'areas' => [
                        ['name' => 'Downtown Dubai', 'shipping_cost' => 20, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Dubai Marina', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Jumeirah', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Palm Jumeirah', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Deira', 'shipping_cost' => 20, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Bur Dubai', 'shipping_cost' => 20, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Barsha', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Jebel Ali', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Business Bay', 'shipping_cost' => 20, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Quoz', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Jumeirah Lake Towers', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Sheikh Zayed Road', 'shipping_cost' => 20, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Garhoud', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Mirdif', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Dubai Silicon Oasis', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Warqa', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'The Greens', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Dubai Hills', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Satwa', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Wasl', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Umm Suqeim', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Sufouh', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Dubai Investment Park', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'The Gardens', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Discovery Gardens', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Furjan', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Jumeirah Village Circle', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Jumeirah Village Triangle', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Motor City', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Dubai Sports City', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Karama', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Oud Metha', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Jaddaf', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Barari', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Nad Al Sheba', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Qusais', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Twar', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Nahda', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'The World Islands', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Palm Jebel Ali', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Hatta',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Hatta Village', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Hatta Dam', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Madam', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Wadi Hatta', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
            ],
            'Sharjah' => [
                [
                    'name' => 'Sharjah City',
                    'shipping_cost' => 30,
                    'shipping_estimate_time' => '1-3',
                    'areas' => [
                        ['name' => 'Al Majaz', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Nahda', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Qasimia', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Taawun', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Khan', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Wahda', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Muwaileh', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Yarmouk', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Ghubaiba', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Jubail', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Musalla', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Nasserya', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Layyah', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Falaj', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Ramla', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Manakh', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Qulayaa', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Azra', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Sabkha', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Gharayen', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                    ],
                ],
                [
                    'name' => 'Khor Fakkan',
                    'shipping_cost' => 50,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Bardi', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Haray', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Safeer', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Hayy Al Haray', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Rabi', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Mudayfi', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Kalba',
                    'shipping_cost' => 50,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Qurm', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Musalla', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Baraha', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Tala', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Saf', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Dibba Al-Hisn',
                    'shipping_cost' => 50,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Shamaliyah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Jazeera', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Hudaibah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Al Dhaid',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Dhaid Central', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Suyoh', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Madam', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
            ],
            'Ajman' => [
                [
                    'name' => 'Ajman City',
                    'shipping_cost' => 30,
                    'shipping_estimate_time' => '1-3',
                    'areas' => [
                        ['name' => 'Al Nuaimia', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Rashidiya', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Mowaihat', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Jurf', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Bustan', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Rumailah', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Rawda', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Zahra', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Sawan', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Hamriya', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                        ['name' => 'Al Nakheel', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Yasmeen', 'shipping_cost' => 25, 'shipping_estimate_time' => '1-2'],
                        ['name' => 'Al Helio', 'shipping_cost' => 30, 'shipping_estimate_time' => '1-3'],
                    ],
                ],
                [
                    'name' => 'Masfout',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Masfout Village', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Manama', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Sabkha', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
            ],
            'Umm Al Quwain' => [
                [
                    'name' => 'Umm Al Quwain City',
                    'shipping_cost' => 40,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Raas', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Salamah', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Raudah', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Maidan', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Dar Al Baida', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Riqqah', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Abar', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Falaj Al Mualla', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
            ],
            'Ras Al Khaimah' => [
                [
                    'name' => 'Ras Al Khaimah City',
                    'shipping_cost' => 40,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Nakheel', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Mairid', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Dhait', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Hamra', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Seer', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Khuzam', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Uraibi', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Qusaidat', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Jazirah Al Hamra', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Rams', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Kharran', 'shipping_cost' => 35, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Sha’am',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Sha’am Village', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Jeer', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Ghail', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
            ],
            'Fujairah' => [
                [
                    'name' => 'Fujairah City',
                    'shipping_cost' => 45,
                    'shipping_estimate_time' => '2-4',
                    'areas' => [
                        ['name' => 'Al Faseel', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Mahatta', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Murbah', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Hayl', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Sakamkam', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Taiba', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Owaid', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                        ['name' => 'Al Sharqi', 'shipping_cost' => 40, 'shipping_estimate_time' => '2-3'],
                    ],
                ],
                [
                    'name' => 'Dibba Al-Fujairah',
                    'shipping_cost' => 50,
                    'shipping_estimate_time' => '2-5',
                    'areas' => [
                        ['name' => 'Al Badiyah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Taween', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Aqah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Dhadna', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Snoopy Island', 'shipping_cost' => 50, 'shipping_estimate_time' => '2-5'],
                    ],
                ],
                [
                    'name' => 'Masafi',
                    'shipping_cost' => 50,
                    'shipping_estimate_time' => '2-5',
                    'areas' => [
                        ['name' => 'Masafi Village', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Halah', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                        ['name' => 'Al Ghumour', 'shipping_cost' => 45, 'shipping_estimate_time' => '2-4'],
                    ],
                ],
            ],
        ];

        foreach ($cities as $governorateName => $cityList) {
            foreach ($cityList as $city) {
                $cityId = DB::table('cities')->insertGetId([
                    'name' => $city['name'],
                    'governorate_id' => $governorateIds[$governorateName],
                    'shipping_cost' => $city['shipping_cost'],
                    'shipping_estimate_time' => $city['shipping_estimate_time'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($city['areas'] as $area) {
                    DB::table('areas')->insert([
                        'city_id' => $cityId,
                        'name' => $area['name'],
                        'shipping_cost' => $area['shipping_cost'],
                        'shipping_estimate_time' => $area['shipping_estimate_time'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
