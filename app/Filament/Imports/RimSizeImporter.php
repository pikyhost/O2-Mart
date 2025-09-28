<?php

namespace App\Filament\Imports;

use App\Models\RimSize;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RimSizeImporter extends Importer
{
    protected static ?string $model = RimSize::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Rim Size'),
        ];
    }

    public function resolveRecord(): ?RimSize
    {
        return RimSize::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Rim Sizes imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
