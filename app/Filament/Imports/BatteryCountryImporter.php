<?php

namespace App\Filament\Imports;

use App\Models\BatteryCountry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatteryCountryImporter extends Importer
{
    protected static ?string $model = BatteryCountry::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Country Name')
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('code')
                ->label('Country Code')
                ->rules(['nullable', 'string', 'max:10']),
        ];
    }

    public function resolveRecord(): ?BatteryCountry
    {
        return BatteryCountry::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Battery Countries imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
