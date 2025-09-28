<?php

namespace App\Filament\Imports;

use App\Models\BatteryCapacity;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatteryCapacityImporter extends Importer
{
    protected static ?string $model = BatteryCapacity::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('value')->label('Battery Capacity')->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?BatteryCapacity
    {
        return BatteryCapacity::firstOrNew([
            'value' => $this->data['value'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Battery Capacities imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
