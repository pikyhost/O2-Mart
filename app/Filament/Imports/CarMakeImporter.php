<?php

namespace App\Filament\Imports;

use App\Models\CarMake;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CarMakeImporter extends Importer
{
    protected static ?string $model = CarMake::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('country')
                ->rules(['max:100']),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?CarMake
    {
        $name = $this->data['name'];
        
        return CarMake::firstOrNew([
            'name' => $name,
        ], [
            'slug' => Str::slug($name),
            'country' => $this->data['country'] ?? null,
            'is_active' => $this->data['is_active'] ?? true,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your car make import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}