<?php

namespace App\Filament\Imports;

use App\Models\Brand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class BrandImporter extends Importer
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Name')
                ->rules(['required', 'string', 'max:150']),

            ImportColumn::make('logo_url')
                ->label('Logo URL (optional)')
                ->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Brand
    {
        return Brand::firstOrNew(['name' => trim($this->data['name'])]);
    }
    
    public function fillRecord(): void
    {
        $this->record->fill([
            'name' => trim($this->data['name']),
        ]);
    }

    public function saveRecord(): void
    {
        $this->record->save();
        
        $logoUrl = $this->data['logo_url'] ?? null;
        if ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            try {
                // Convert Google Drive sharing link to direct download link
                if (strpos($logoUrl, 'drive.google.com') !== false) {
                    preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $logoUrl, $matches);
                    if (isset($matches[1])) {
                        $logoUrl = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
                    }
                }
                
                $this->record->clearMediaCollection('logo');
                $this->record->addMediaFromUrl($logoUrl)->toMediaCollection('logo');
            } catch (\Exception $e) {
                \Log::warning('Failed to import logo', ['brand' => $this->record->name, 'error' => $e->getMessage()]);
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Brands imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }


}
