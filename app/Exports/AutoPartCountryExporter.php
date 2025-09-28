<?php

namespace App\Exports;

use App\Models\AutoPartCountry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AutoPartCountryExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AutoPartCountry::select('id', 'name', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }
}
