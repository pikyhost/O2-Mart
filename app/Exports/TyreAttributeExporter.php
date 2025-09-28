<?php

namespace App\Filament\Exports;

use App\Models\TyreAttribute;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TyreAttributeExporter implements FromCollection, WithHeadings
{
    protected static ?string $model = TyreAttribute::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('carMake.name')->label('Car Make'),
            ExportColumn::make('carModel.name')->label('Car Model'),
            ExportColumn::make('model_year')->label('Model Year'),
            ExportColumn::make('trim')->label('Trim'),
            ExportColumn::make('tyre_attribute')->label('Tyre Attribute'),
            ExportColumn::make('tyre_oem')->label('Tyre OEM'),
        ];
    }

    public function collection()
    {
        return TyreAttribute::with(['carMake', 'carModel'])->get();
    }

    public function headings(): array
    {
        return [
            'Car Make',
            'Car Model',
            'Model Year',
            'Trim',
            'Tyre Attribute',
            'Tyre OEM',
        ];
    }
}
