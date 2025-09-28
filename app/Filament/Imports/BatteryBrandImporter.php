<?php

namespace App\Filament\Imports;

use App\Models\BatteryBrand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatteryBrandImporter extends Importer
{
    protected static ?string $model = BatteryBrand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Battery Brand')
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (BatteryBrand $record, $state) {
                    $record->value = $state;
                }),

            ImportColumn::make('logo_url')
                ->label('Logo URL')
                ->fillRecordUsing(function () {
                    // Don't fill the record, just store for afterSave
                }),
        ];
    }

    public function resolveRecord(): ?BatteryBrand
    {
        return BatteryBrand::firstOrNew([
            'value' => $this->data['name'],
        ]);
    }

    public function afterSave(): void
    {
        if (!empty($this->data['logo_url']) && filter_var($this->data['logo_url'], FILTER_VALIDATE_URL)) {
            try {
                $this->record->clearMediaCollection('logo');
                $this->record->save();

                $url = $this->convertGoogleDriveUrl($this->data['logo_url']);
                
                $this->record
                    ->addMediaFromUrl($url)
                    ->toMediaCollection('logo');
                    
                $this->record->refresh();
            } catch (\Throwable $e) {
                \Log::error("Failed to import battery brand logo [{$this->record->value}]: " . $e->getMessage());
            }
        }
    }
    
    private function convertGoogleDriveUrl(string $url): string
    {
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://drive.google.com/uc?export=download&id={$matches[1]}";
        }
        return $url;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Battery Brands imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
