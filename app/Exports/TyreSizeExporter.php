<?php

namespace App\Exports;

use App\Models\TyreSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TyreSizeExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TyreSize::select('id', 'size', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Tyre Size', 'Created At'];
    }
}
