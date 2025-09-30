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
            ImportColumn::make('Model Year')->requiredMapping()->rules(['required', 'integer', 'min:1990', 'max:' . date('Y')]),
            ImportColumn::make('Attribute Name')->requiredMapping()->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?RimAttribute
    {
        $make = CarMake::firstOrCreate(['name' => trim($this->data['Car Make'])]);
        $modelName = trim($this->data['Car Model']);
        $model = CarModel::firstOrCreate([
            'name' => $modelName,
            'car_make_id' => $make->id,
        ], [
            'name' => $modelName,
            'car_make_id' => $make->id,
            'slug' => \Illuminate\Support\Str::slug($modelName),
        ]);

        return RimAttribute::firstOrNew([
            'car_make_id' => $make->id,
            'car_model_id' => $model->id,
            'model_year' => $this->data['Model Year'],
            'name' => trim($this->data['Attribute Name']),
        ]);
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
