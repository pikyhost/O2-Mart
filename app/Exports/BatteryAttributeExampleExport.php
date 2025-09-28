<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BatteryAttributeExampleExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect([
            [
                'Toyota', // Car Make
                'Corolla', // Car Model
                2022,      // Model Year
                'CCA 600A', // Attribute Name
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Car Make',
            'Car Model',
            'Model Year',
            'Attribute Name',
        ];
    }
}
