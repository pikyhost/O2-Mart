<?php

namespace App\Exports;

use App\Models\ViscosityGrade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ViscosityGradeExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ViscosityGrade::select('id', 'name', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }
}
