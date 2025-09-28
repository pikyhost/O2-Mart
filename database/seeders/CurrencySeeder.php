<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code'      => 'USD',
                'name'      => 'US Dollar',
                'symbol'    => '$',
                'is_active' => true,
            ],
            [
                'code'      => 'EUR',
                'name'      => 'Euro',
                'symbol'    => '€',
                'is_active' => true,
            ],
            [
                'code'      => 'EGP',
                'name'      => 'Egyptian Pound',
                'symbol'    => 'E£',
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']], // Prevent duplication
                $currency
            );
        }
    }
}
