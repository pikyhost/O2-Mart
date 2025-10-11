<?php

namespace App\Filament\Imports;

use App\Models\TyreAttribute;
use App\Models\CarMake;
use App\Models\CarModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TyreAttributeImporter extends Importer
{
    protected static ?string $model = TyreAttribute::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('Car Make')->requiredMapping(),
            ImportColumn::make('Car Model')->requiredMapping(),
            ImportColumn::make('Model Year')->requiredMapping(),
            ImportColumn::make('Trim'),
            ImportColumn::make('Tyre Attribute'),
            ImportColumn::make('Rare Attribute'),
            ImportColumn::make('Tyre OEM'),
        ];
    }

    public function resolveRecord(): ?TyreAttribute
    {
        if (
            empty($this->data['Car Make']) ||
            empty($this->data['Car Model']) ||
            empty($this->data['Model Year'])
        ) {
            return null;
        }

        return new TyreAttribute();
    }

    public function fillRecord(): void
    {
        $carMake = CarMake::firstOrCreate(
            ['name' => trim($this->data['Car Make'])],
            ['slug' => \Illuminate\Support\Str::slug(trim($this->data['Car Make'])), 'is_active' => true]
        );
        $carModel = CarModel::firstOrCreate(
            ['name' => trim($this->data['Car Model']), 'car_make_id' => $carMake->id],
            ['slug' => \Illuminate\Support\Str::slug(trim($this->data['Car Model'])), 'is_active' => true]
        );

        $this->record->fill([
            'car_make_id'    => $carMake->id,
            'car_model_id'   => $carModel->id,
            'model_year'     => trim($this->data['Model Year']),
            'trim'           => $this->data['Trim'] ?? '',
            'tyre_attribute' => $this->data['Tyre Attribute'] ?? '',
            'rare_attribute' => $this->data['Rare Attribute'] ?? '',
            'tyre_oem'       => (bool) ($this->data['Tyre OEM'] ?? 0),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tyre attributes imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
