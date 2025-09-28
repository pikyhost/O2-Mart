<?php

namespace App\Filament\Imports;

use App\Models\BatteryDimension;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatteryDimensionImporter extends Importer
{
    protected static ?string $model = BatteryDimension::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('value')->label('Battery Dimension')->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?BatteryDimension
    {
        return BatteryDimension::firstOrNew([
            'value' => $this->data['value'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Battery Dimensions imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
