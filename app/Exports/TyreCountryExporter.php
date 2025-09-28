<?php

namespace App\Exports;

use App\Models\TyreCountry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TyreCountryExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TyreCountry::select('id', 'name', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Country Name', 'Created At'];
    }
}
