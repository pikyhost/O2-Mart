<?php

namespace App\Filament\Imports;

use App\Models\AutoPart;
use App\Models\Category;
use App\Models\AutoPartBrand;
use App\Models\AutoPartCountry;
use App\Models\ViscosityGrade;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class AutoPartImporter extends BaseUpsertImporter
{
    use ImportHelpers;

    protected static ?string $model = AutoPart::class;

    protected static array $uniqueBy = ['sku'];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'max:255']),
            ImportColumn::make('slug')->rules(['nullable', 'max:255']),
            ImportColumn::make('sku')->rules(['nullable', 'max:255']),

            ImportColumn::make('parent_category_name')->label('Parent Category'),
            ImportColumn::make('sub_category_name')->label('Sub Category'),

            ImportColumn::make('auto_part_brand_name')->label('Brand'),
            ImportColumn::make('auto_part_country_name')->label('Country'),
            ImportColumn::make('viscosity_grade_name')->label('Viscosity'),

            ImportColumn::make('price_including_vat')->rules(['nullable','numeric']),
            ImportColumn::make('discount_percentage')->rules(['nullable','numeric']),
            ImportColumn::make('discounted_price')->rules(['nullable','numeric']),
            ImportColumn::make('weight')->rules(['nullable','numeric']),
            ImportColumn::make('height')->rules(['nullable','numeric']),
            ImportColumn::make('width')->rules(['nullable','numeric']),
            ImportColumn::make('length')->rules(['nullable','numeric']),

            ImportColumn::make('details'),
            ImportColumn::make('description'),
            ImportColumn::make('photo_alt_text'),
            ImportColumn::make('photo_link')->label('Feature Image URL'),

            // ImportColumn::make('buy_3_get_1_free')->label('Buy 3 Get 1 Free'),
            ImportColumn::make('gallery_links')->label('Gallery Links (comma separated)'),
            ImportColumn::make('secondary_image_url')->label('Secondary Image URL'),
            
            ImportColumn::make('meta_title')->rules(['nullable', 'max:255']),
            ImportColumn::make('meta_description')->rules(['nullable', 'max:500']),
        ];
    }

    public function fillRecord(): void
    {
        parent::fillRecord();

        if (empty($this->record->slug) && !empty($this->record->name)) {
            $this->record->slug = Str::slug($this->record->name);
        } else {
            $this->record->slug = Str::slug(
                preg_replace('/\s+/', ' ', trim($this->record->slug ?? $this->record->name))
            );
        }

        $price    = (float)($this->data['price_including_vat'] ?? 0);
        $discount = (float)($this->data['discount_percentage'] ?? 0);

        // Only calculate discounted price if both price and discount are provided
        if ($price > 0 && $discount > 0) {
            $this->record->discounted_price = round($price - ($price * ($discount / 100)), 2);
        }
        // Don't auto-set discounted_price to regular price - only save what's imported

        $this->record->buy_3_get_1_free = $this->parseBool($this->data['buy_3_get_1_free'] ?? false);

        // Photo link will be handled in saveRecord via media library

        if (!empty($this->data['secondary_image_url'])) {
            $this->record->secondary_image_url = trim($this->data['secondary_image_url']);
        }

        if (!empty($this->data['gallery_links'])) {
            $this->record->gallery_links = $this->data['gallery_links'];
        }

        // Handle categories
        $parent = null;
        $categoryId = null;

        $this->whenFilled('parent_category_name', function ($name) use (&$parent) {
            $parent = $this->firstOrCreateByName(Category::class, $name);
        });

        $this->whenFilled('sub_category_name', function ($name) use (&$parent, &$categoryId) {
            $sub = $this->firstOrCreateByName(Category::class, $name, $parent?->id);
            if ($sub) $categoryId = $sub->id;
        });

        if (!$categoryId && $parent) {
            $categoryId = $parent->id;
        }

        if ($categoryId) {
            $this->record->category_id = $categoryId;
        }

        // Handle relationships in fillRecord since handleRecord is not called
        $this->whenFilled('auto_part_brand_name', function ($name) {
            $brand = $this->firstOrCreateByName(AutoPartBrand::class, $name);
            if ($brand) $this->record->auto_part_brand_id = $brand->id;
        });

        $this->whenFilled('auto_part_country_name', function ($name) {
            $country = $this->firstOrCreateByName(AutoPartCountry::class, $name);
            if ($country) $this->record->auto_part_country_id = $country->id;
        });

        $this->whenFilled('viscosity_grade_name', function ($name) {
            $visc = $this->firstOrCreateByName(ViscosityGrade::class, $name);
            if ($visc) $this->record->viscosity_grade_id = $visc->id;
        });
    }

    public function saveRecord(): void
    {
        $this->record->save();

        // Import feature image
        if (!empty($this->data['photo_link'])) {
            $url = trim($this->data['photo_link']);
            try {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $this->record->addMediaFromUrl($url)
                        ->toMediaCollection('auto_part_feature_image');
                }
            } catch (\Exception $e) {
                \Log::error('AutoPartImporter: Failed to import feature image', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Import secondary image
        if (!empty($this->data['secondary_image_url'])) {
            $url = trim($this->data['secondary_image_url']);
            try {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $this->record->addMediaFromUrl($url)
                        ->toMediaCollection('auto_part_secondary_image');
                }
            } catch (\Exception $e) {
                \Log::error('AutoPartImporter: Failed to import secondary image', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Auto parts import completed. ' . number_format($import->successful_rows) . ' '
            . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' failed.';
        }

        return $body;
    }
}
