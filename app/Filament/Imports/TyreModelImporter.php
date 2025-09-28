<?php

namespace App\Filament\Imports;

use App\Models\TyreModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TyreModelImporter extends Importer
{
    protected static ?string $model = TyreModel::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'string', 'max:255']),
        ];
    }

    public function resolveRecord(): ?TyreModel
    {
        return TyreModel::firstOrNew([
            'name' => trim($this->data['name']),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tyre models imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
