<?php

namespace App\Filament\Imports;

use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\RimAttribute;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RimAttributeImporter extends Importer
{
    protected static ?string $model = RimAttribute::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('Car Make')->requiredMapping()->rules(['required', 'string']),
            ImportColumn::make('Car Model')->requiredMapping()->rules(['required', 'string']),
            ImportColumn::make('Model Year')->requiredMapping()->rules(['required', 'integer']),
            ImportColumn::make('Attribute Name')->requiredMapping()->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?RimAttribute
    {
        $makeName = trim($this->data['Car Make']);
        $make = CarMake::where('name', $makeName)->first();
        if (!$make) {
            $make = CarMake::create([
                'name' => $makeName,
                'slug' => \Illuminate\Support\Str::slug($makeName),
            ]);
        }
        
        $modelName = trim($this->data['Car Model']);
        $model = CarModel::where('name', $modelName)
            ->where('car_make_id', $make->id)
            ->first();
            
        if (!$model) {
            $model = CarModel::create([
                'name' => $modelName,
                'car_make_id' => $make->id,
                'slug' => \Illuminate\Support\Str::slug($modelName),
            ]);
        }

        return RimAttribute::firstOrNew([
            'car_make_id' => $make->id,
            'car_model_id' => $model->id,
            'model_year' => $this->data['Model Year'],
            'name' => trim($this->data['Attribute Name']),
        ]);
    }

    public function fillRecord(): void
    {
        // Don't call parent::fillRecord() to prevent CSV columns from being filled
        // All data is handled in resolveRecord()
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Rim Attributes imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
