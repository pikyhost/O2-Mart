<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BatteryExampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'name' => 'ACDelco 70AH',
                'slug' => 'acdelco-70ah',
                'item_code' => 'BAT-1234',
                'sku' => 'SKU-5678',
                'category_id' => 1,
                'country_id' => 2,
                'warranty' => '12 months',
                'battery_brand_id' => 1,
                'capacity_id' => 1,
                'dimension_id' => 1,
                'regular_price' => 350,
                'discount_percentage' => 10,
                'discounted_price' => 315,
                'description' => 'High performance maintenance-free battery.',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'slug',
            'item_code',
            'sku',
            'category_id',
            'country_id',
            'warranty',
            'battery_brand_id',
            'capacity_id',
            'dimension_id',
            'regular_price',
            'discount_percentage',
            'discounted_price',
            'description',
        ];
    }
}
