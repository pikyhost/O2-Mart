<?php

namespace App\Filament\Imports;

use App\Models\BatteryAttribute;
use App\Models\CarMake;
use App\Models\CarModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatteryAttributeImporter extends Importer
{
    protected static ?string $model = BatteryAttribute::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('Car Make')
                ->label('Car Make')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('Car Model')
                ->label('Car Model')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('Model Year')
                ->label('Model Year')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:1990']),
            ImportColumn::make('Attribute Name')
                ->label('Attribute Name')
                ->requiredMapping()
                ->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?BatteryAttribute
    {
        return new BatteryAttribute();
    }

    public function fillRecord(): void
    {
        $make = CarMake::firstOrCreate(['name' => trim($this->data['Car Make'])]);
        $model = CarModel::firstOrCreate([
            'name' => trim($this->data['Car Model']),
            'car_make_id' => $make->id,
        ]);

        $this->record->fill([
            'car_make_id' => $make->id,
            'car_model_id' => $model->id,
            'model_year' => $this->data['Model Year'],
            'name' => $this->data['Attribute Name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Battery Attributes imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
