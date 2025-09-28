<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BatteryDimensionExampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            ['value' => 'Example Dimension'],
        ]);
    }

    public function headings(): array
    {
        return ['value'];
    }
}
