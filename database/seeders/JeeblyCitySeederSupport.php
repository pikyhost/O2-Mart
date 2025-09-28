<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class JeeblyCitySeeder extends Seeder
{
    public function run(): void
    {
        City::query()->update([
            'is_supported_by_jeebly' => true,
        ]);

        $this->command->info('✅ All cities updated: is_supported_by_jeebly = true');
    }
}
