<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RimCountryExampleExport implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['name' => 'UAE'],
        ]);
    }
}
