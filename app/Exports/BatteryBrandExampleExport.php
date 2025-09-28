<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BatteryBrandExampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            ['value' => 'Example Brand'],
        ]);
    }

    public function headings(): array
    {
        return ['value'];
    }
}
