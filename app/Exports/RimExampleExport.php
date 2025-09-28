<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class RimExampleExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'Product Name', 'Colour', 'Condition', 'Specification', 'Bolt Pattern',
                'RIM Size', 'Offsets', 'Centre Caps', 'Wheel Attribute', 'Set of 4',
                'Item Code', 'SKU', 'Warranty', 'Brand ID', 'Country ID', 'Weight (KG) -set of 4',
                'Regular Price', 'Discounted Price', 'Discount Percent', 'Product Full Description',
                'Alt Text'
            ],
            [
                'Rim ABC', 'Black', 'New', 'Alloy', '5x114.3',
                '18"', '35', 'Yes', 1, true,
                'RIM-001', 'SKU-001', '1 Year', 1, 1, 25,
                1000, 900, 10, 'Top-quality rim for performance cars', 'High Performance Rim'
            ],
        ];
    }
}
