<?php

namespace App\Filament\Imports;

use App\Models\City;
use App\Models\Governorate;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CityImporter extends Importer
{
    protected static ?string $model = City::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('governorate_name')
                ->label('Governorate Name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?City
    {
        return $this->handleRecordCreation($this->data);
    }
    
    protected function handleRecordCreation(array $data): City
    {
        $governorateName = $data['governorate_name'];
        $cityName = $data['name'];
        
        $governorate = Governorate::firstOrCreate([
            'name' => $governorateName,
        ]);

        return City::updateOrCreate([
            'governorate_id' => $governorate->id,
            'name' => $cityName,
        ], [
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your city import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}