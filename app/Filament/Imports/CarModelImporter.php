<?php

namespace App\Filament\Imports;

use App\Models\CarModel;
use App\Models\CarMake;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CarModelImporter extends Importer
{
    protected static ?string $model = CarModel::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('make_name')
                ->label('Make Name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn () => null), // Don't fill the record with this value
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('year_from')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)]),
            ImportColumn::make('year_to')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)]),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?CarModel
    {
        return $this->handleRecordCreation($this->data);
    }
    
    protected function handleRecordCreation(array $data): CarModel
    {
        $makeName = $data['make_name'];
        $modelName = $data['name'];
        
        // Find or create the car make
        $carMake = CarMake::firstOrCreate([
            'name' => $makeName,
        ], [
            'slug' => Str::slug($makeName),
            'is_active' => true,
        ]);

        // Create or update car model
        return CarModel::updateOrCreate([
            'car_make_id' => $carMake->id,
            'name' => $modelName,
        ], [
            'slug' => Str::slug($modelName),
            'year_from' => $data['year_from'] ?? 2000,
            'year_to' => $data['year_to'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your car model import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}