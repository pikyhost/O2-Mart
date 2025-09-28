<?php

namespace App\Filament\Imports;

use App\Models\RimBrand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RimBrandImporter extends Importer
{
    protected static ?string $model = RimBrand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Rim Brand')
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('logo_url')
                ->label('Logo URL')
                ->fillRecordUsing(function () {
                    // Don't fill the record, just store for afterSave
                }),
        ];
    }

    public function resolveRecord(): ?RimBrand
    {
        return RimBrand::firstOrNew([
            'name' => $this->data['name'],
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
                \Log::error("Failed to import rim brand logo [{$this->record->name}]: ".$e->getMessage());
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
        $body = 'Rim Brands imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}
