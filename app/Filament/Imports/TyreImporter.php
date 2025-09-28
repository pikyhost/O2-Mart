<?php

namespace App\Filament\Imports;

use App\Models\Tyre;
use App\Models\TyreBrand;
use App\Models\TyreModel;
use App\Models\TyreCountry;
use App\Models\TyreSize;
use App\Models\TyreAttribute;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TyreImporter extends Importer
{
    protected static ?string $model = Tyre::class;
    
    public static function getChunkSize(): int
    {
        return 25;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->rules(['nullable']),
            ImportColumn::make('slug')->rules(['nullable']),
            ImportColumn::make('sku')->requiredMapping()->rules(['required', 'string', 'min:1']),
            ImportColumn::make('tyreBrand')->label('Brand'),
            ImportColumn::make('tyreModel')->label('Model'),
            ImportColumn::make('tyreCountry')->label('Country'),
            ImportColumn::make('tyreSize')->label('Size'),
            ImportColumn::make('tyreAttribute')->label('Attribute'),
            ImportColumn::make('width')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('height')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('wheel_diameter')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('model'),
            ImportColumn::make('load_index'),
            ImportColumn::make('speed_rating'),
            ImportColumn::make('weight_kg')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('production_year')
                ->rules(['nullable', 'integer'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9]/', '', (string)$state);
                    return is_numeric($cleaned) ? (int)$cleaned : null;
                }),
            ImportColumn::make('warranty'),
            ImportColumn::make('price_vat_inclusive')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('discount_percentage')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = str_replace(['%', ' '], '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('discounted_price')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('alt_text'),
            ImportColumn::make('feature_image'),
            ImportColumn::make('secondary_image'),
            ImportColumn::make('gallery'),
            ImportColumn::make('buy_3_get_1_free'),
            
            ImportColumn::make('meta_title')->rules(['nullable', 'max:255']),
            ImportColumn::make('meta_description')->rules(['nullable', 'max:500']),
            ImportColumn::make('alt_text_seo')->label('Alt Text SEO')->rules(['nullable', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $record->alt_text = $state;
                }),
        ];
    }

    public function resolveRecord(): ?Tyre
    {
        // Skip empty rows
        if (empty($this->data['sku'])) {
            return null;
        }
        
        return Tyre::firstOrNew(['sku' => $this->data['sku']]);
    }

    public function fillRecord(): void
    {
        // Skip if no SKU
        if (empty($this->data['sku'])) {
            return;
        }
        
        $this->record->fill([
            'title' => $this->data['title'] ?? null,
            'slug' => $this->data['slug'] ?? null,
            'sku' => $this->data['sku'],
            'model' => $this->data['model'] ?? null,
            'width' => $this->data['width'] ?? null,
            'height' => $this->data['height'] ?? null,
            'wheel_diameter' => $this->data['wheel_diameter'] ?? null,
            'load_index' => $this->data['load_index'] ?? null,
            'speed_rating' => $this->data['speed_rating'] ?? null,
            'weight_kg' => $this->data['weight_kg'] ?? null,
            'production_year' => $this->data['production_year'] ?? null,
            'warranty' => $this->data['warranty'] ?? null,
            'price_vat_inclusive' => $this->data['price_vat_inclusive'] ?? null,
            'discount_percentage' => $this->data['discount_percentage'] ?? null,
            'alt_text' => $this->data['alt_text'] ?? $this->data['alt_text_seo'] ?? null,
            'buy_3_get_1_free' => !empty($this->data['buy_3_get_1_free']) && strtolower($this->data['buy_3_get_1_free']) === 'yes',
            'meta_title' => $this->data['meta_title'] ?? null,
            'meta_description' => $this->data['meta_description'] ?? null,
        ]);
        
        // Calculate discounted price only if both price and discount are provided
        $price = (float)($this->data['price_vat_inclusive'] ?? 0);
        $discount = (float)($this->data['discount_percentage'] ?? 0);
        
        if ($price > 0 && $discount > 0) {
            $this->record->discounted_price = round($price - ($price * ($discount / 100)), 2);
        } else {
            $this->record->discounted_price = $this->data['discounted_price'] ?? null;
        }
        // Don't auto-set discounted_price to regular price - only save what's imported

        // Generate unique slug - use slug, title, or SKU as fallback
        $baseSlug = $this->data['slug'] ?: $this->record->title ?: $this->record->sku ?: 'tyre-' . time();
        $this->record->slug = $this->generateUniqueSlug($baseSlug);

        // Handle relationships
        if (!empty($this->data['tyreBrand'])) {
            $brand = TyreBrand::firstOrCreate(['name' => trim($this->data['tyreBrand'])]);
            $this->record->tyre_brand_id = $brand->id;
        }

        if (!empty($this->data['tyreModel'])) {
            $model = TyreModel::firstOrCreate(['name' => trim($this->data['tyreModel'])]);
            $this->record->tyre_model_id = $model->id;
        }

        if (!empty($this->data['tyreCountry'])) {
            $country = TyreCountry::firstOrCreate(['name' => trim($this->data['tyreCountry'])]);
            $this->record->tyre_country_id = $country->id;
        }

        if (!empty($this->data['tyreSize'])) {
            $tyreSize = TyreSize::firstOrCreate(['size' => trim($this->data['tyreSize'])]);
            $this->record->tyre_size_id = $tyreSize->id;
        }

        if (!empty($this->data['tyreAttribute'])) {
            // Get or create default car make and model
            $defaultMake = \App\Models\CarMake::firstOrCreate(
                ['name' => 'Universal'],
                ['slug' => 'universal', 'is_active' => true]
            );
            
            $defaultModel = \App\Models\CarModel::firstOrCreate(
                ['name' => 'Universal', 'car_make_id' => $defaultMake->id],
                ['slug' => 'universal', 'year_from' => '2000', 'year_to' => '2030', 'is_active' => true]
            );
            
            $attr = TyreAttribute::firstOrCreate(
                ['tyre_attribute' => trim($this->data['tyreAttribute'])],
                [
                    'car_make_id' => $defaultMake->id, 
                    'car_model_id' => $defaultModel->id, 
                    'model_year' => '2024'
                ]
            );
            $this->record->tyre_attribute_id = $attr->id;
        }
    }

    public function saveRecord(): void
    {
        $this->record->save();
        $this->processImages();
    }

    private function processImages(): void
    {
        if (!empty($this->data['feature_image'])) {
            $this->addImageFromUrl($this->data['feature_image'], 'tyre_feature_image');
        }
        
        if (!empty($this->data['secondary_image'])) {
            $this->addImageFromUrl($this->data['secondary_image'], 'tyre_secondary_image');
        }
        
        if (!empty($this->data['gallery'])) {
            $urls = explode(',', $this->data['gallery']);
            foreach (array_filter($urls) as $url) {
                $this->addImageFromUrl(trim($url), 'tyre_gallery');
            }
        }
    }
    
    private function addImageFromUrl(string $url, string $collection): void
    {
        \Log::info('TyreImporter: Attempting to import image', ['url' => $url, 'collection' => $collection, 'tyre_id' => $this->record->id]);
        
        try {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $media = $this->record->addMediaFromUrl($url)->toMediaCollection($collection);
                \Log::info('TyreImporter: Successfully imported image', ['url' => $url, 'tyre_id' => $this->record->id, 'media_id' => $media->id]);
            } else {
                \Log::warning('TyreImporter: Invalid image URL', ['url' => $url, 'tyre_id' => $this->record->id]);
            }
        } catch (\Exception $e) {
            \Log::error('TyreImporter: Failed to import image', ['url' => $url, 'tyre_id' => $this->record->id, 'error' => $e->getMessage()]);
        }
    }
    
    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = Str::slug($baseSlug);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Tyre::where('slug', $slug)->where('id', '!=', $this->record->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tyres imported successfully: ' . number_format($import->successful_rows) . ' rows.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' Failed rows: ' . number_format($failed);
        }
        return $body;
    }
}