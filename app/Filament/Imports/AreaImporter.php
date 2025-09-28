<?php

namespace App\Filament\Imports;

use App\Models\Area;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AreaImporter extends Importer
{
    protected static ?string $model = Area::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('city_name')
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('governorate_name')
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('shipping_cost')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('is_remote')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?Area
    {
        // Find UAE country by code
        $country = \App\Models\Country::where('code', 'AE')->first();
        if (!$country) {
            throw new \Exception('UAE country not found. Please ensure country with code "AE" exists.');
        }
        
        // Find or create governorate by name with UAE country
        $governorate = \App\Models\Governorate::firstOrCreate(
            ['name' => $this->data['governorate_name']],
            ['country_id' => $country->id]
        );
        
        // Find or create city by name and governorate
        $city = \App\Models\City::firstOrCreate(
            ['name' => $this->data['city_name'], 'governorate_id' => $governorate->id]
        );
        
        $area = Area::firstOrNew([
            'name' => $this->data['name'],
            'city_id' => $city->id,
        ]);
        
        // Set defaults if not provided
        if (!isset($this->data['is_active'])) {
            $area->is_active = true;
        }
        
        if (!isset($this->data['is_remote'])) {
            $area->is_remote = false;
        }
        
        return $area;
    }

    public function fillRecord(): void
    {
        // Only fill actual database columns, exclude temporary fields
        $this->record->fill([
            'name' => $this->data['name'],
            'shipping_cost' => $this->data['shipping_cost'] ?? null,
            'is_active' => $this->data['is_active'] ?? true,
            'is_remote' => $this->data['is_remote'] ?? false,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your area import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
