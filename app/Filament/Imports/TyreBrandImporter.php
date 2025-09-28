<?php

namespace App\Filament\Imports;

use App\Models\TyreBrand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TyreBrandImporter extends Importer
{
    protected static ?string $model = TyreBrand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('logo_url')
                ->label('Logo URL'),
        ];
    }

    public function resolveRecord(): ?TyreBrand
    {
        return TyreBrand::firstOrNew([
            'name' => trim($this->data['name']),
        ]);
    }

    public function fillRecord(): void
    {
        // Only fill the name field, not logo_url since it's not a database column
        $this->record->fill([
            'name' => trim($this->data['name']),
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
                \Log::error("Failed to import tyre brand logo [{$this->record->name}]: " . $e->getMessage());
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
        $body = 'Tyre brands imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
