<?php

namespace App\Exports;

use App\Models\TyreModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TyreModelExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TyreModel::select('id', 'name', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Model Name', 'Created At'];
    }
}
