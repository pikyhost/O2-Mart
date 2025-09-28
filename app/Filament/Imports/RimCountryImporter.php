<?php

namespace App\Filament\Imports;

use App\Models\RimCountry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RimCountryImporter extends Importer
{
    protected static ?string $model = RimCountry::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Rim Country'),
        ];
    }

    public function resolveRecord(): ?RimCountry
    {
        return RimCountry::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Rim Countries imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
