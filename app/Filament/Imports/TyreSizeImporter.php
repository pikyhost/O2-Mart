<?php

namespace App\Filament\Imports;

use App\Models\TyreSize;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TyreSizeImporter extends Importer
{
    protected static ?string $model = TyreSize::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('size')
                ->rules(['required', 'string', 'max:255']),
        ];
    }

    public function resolveRecord(): ?TyreSize
    {
        return TyreSize::firstOrNew([
            'size' => trim($this->data['size']),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tyre sizes imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
