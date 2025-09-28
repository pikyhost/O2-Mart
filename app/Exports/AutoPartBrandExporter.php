<?php

namespace App\Exports;

use App\Models\AutoPartBrand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AutoPartBrandExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AutoPartBrand::select('id', 'name', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }
}
