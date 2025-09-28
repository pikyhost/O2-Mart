<?php

namespace App\Filament\Imports;

use App\Models\AutoPartCountry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AutoPartCountryImporter extends Importer
{
    protected static ?string $model = AutoPartCountry::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Country Name')
        ];
    }

    public function resolveRecord(): ?AutoPartCountry
    {
        return AutoPartCountry::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your import has completed successfully. '
            . number_format($import->successful_rows) . ' '
            . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' '
                . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
