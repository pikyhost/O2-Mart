<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TyreBrandExporter implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['name' => 'Brand'],
        ]);
    }
}
