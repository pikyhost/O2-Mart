<?php

namespace App\Filament\Imports;

use App\Models\TyreCountry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TyreCountryImporter extends Importer
{
    protected static ?string $model = TyreCountry::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'string', 'max:255']),
        ];
    }

    public function resolveRecord(): ?TyreCountry
    {
        return TyreCountry::firstOrNew([
            'name' => trim($this->data['name']),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tyre countries imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
