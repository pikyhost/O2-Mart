<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RimSizeExampleExport implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['size' => '17 Inch'],
        ]);
    }
}
