<?php

namespace App\Filament\Imports;

use App\Models\ViscosityGrade;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ViscosityGradeImporter extends Importer
{
    protected static ?string $model = ViscosityGrade::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Viscosity Grade'),
        ];
    }

    public function resolveRecord(): ?ViscosityGrade
    {
        return ViscosityGrade::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Viscosity Grades imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }

        return $body;
    }
}
