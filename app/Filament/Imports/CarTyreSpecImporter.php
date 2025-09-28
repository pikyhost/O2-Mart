<?php

namespace App\Filament\Imports;

use App\Models\CarTyreSpec;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CarTyreSpecImporter extends Importer implements WithChunkReading, WithBatchInserts
{
    protected static ?string $model = CarTyreSpec::class;

    public function __construct(Import $import, array $columnMap, array $options)
    {
        parent::__construct($import, $columnMap, $options);

        // Increase timeout and memory for large imports
        set_time_limit(3600);
        ini_set('memory_limit', '512M');
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('car_make')
                ->label('Car Make')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Toyota'),

            ImportColumn::make('car_model')
                ->label('Car Model')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Camry'),

            ImportColumn::make('car_year')
                ->label('Manufacture Year')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'between:1900,' . now()->year])
                ->example('2020'),

            ImportColumn::make('engine_performance')
                ->label('Engine Performance')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('2.5L 4-cylinder'),

            ImportColumn::make('tyre_size')
                ->label('Tyre Size')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('215/55R17'),

            ImportColumn::make('tyre_oem')
                ->label('OEM Tyre')
                ->rules(['nullable', 'string', 'in:OEM,Non-OEM,Yes,No'])
                ->example('OEM'),
        ];
    }

    public function resolveRecord(): ?Model
    {
        // Find existing record or create new one
        return CarTyreSpec::firstOrNew([
            'car_make' => $this->data['car_make'],
            'car_model' => $this->data['car_model'],
            'car_year' => $this->data['car_year'],
        ]);
    }

    public function beforeCreate(): void
    {
        // Additional processing before creating the record
        $this->data['tyre_oem'] = $this->normalizeTyreOem($this->data['tyre_oem'] ?? null);
    }

    protected function normalizeTyreOem(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match (strtolower($value)) {
            'yes', 'oem' => 'OEM',
            'no', 'non-oem' => 'Non-OEM',
            default => $value,
        };
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }

    public function batchSize(): int
    {
        return 100; // Insert 100 records at a time
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your car tyre spec import has completed. ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' ' . Str::plural('row', $failed) . ' failed.';
        }

        return $body;
    }
}
