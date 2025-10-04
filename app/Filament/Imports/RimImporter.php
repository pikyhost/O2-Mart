<?php

namespace App\Filament\Imports;

use App\Models\Rim;
use App\Models\RimBrand;
use App\Models\RimSize;
use App\Models\RimAttribute;
use App\Models\RimCountry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RimImporter extends BaseUpsertImporter
{
    use ImportHelpers;

    protected static ?string $model = Rim::class;
    protected static array $uniqueBy = ['name'];
    
    protected array $rimAttributesToSync = [];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required']),
            ImportColumn::make('slug')->rules(['nullable']),
            ImportColumn::make('alt_text'),
            ImportColumn::make('description'),
            ImportColumn::make('rimBrand')->label('Brand'),
            ImportColumn::make('rimSize')->label('RIM Size'),
            ImportColumn::make('rimAttribute')->label('Wheel Attribute'),
            ImportColumn::make('rimCountry')->label('Country of Origin'),
            ImportColumn::make('colour'),
            ImportColumn::make('condition'),
            ImportColumn::make('specification'),
            ImportColumn::make('bolt_pattern'),
            ImportColumn::make('offsets'),
            ImportColumn::make('centre_caps'),
            ImportColumn::make('is_set_of_4'),
            ImportColumn::make('item_code'),
            ImportColumn::make('sku')->rules(['nullable']),
            ImportColumn::make('warranty'),
            ImportColumn::make('weight')->rules(['nullable', 'numeric']),
            ImportColumn::make('regular_price')->rules(['nullable', 'numeric']),
            ImportColumn::make('discounted_price')->rules(['nullable', 'numeric']),
            ImportColumn::make('discount_percent')
                ->rules(['nullable', 'numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = str_replace(['%', ' '], '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('product_image_url'),
            
            ImportColumn::make('meta_title')->rules(['nullable', 'max:255']),
            ImportColumn::make('meta_description')->rules(['nullable', 'max:500']),
            ImportColumn::make('alt_text_seo')->label('Alt Text SEO')->rules(['nullable', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $record->alt_text = $state;
                }),

        ];
    }

    public function fillRecord(): void
    {
        // Don't call parent::fillRecord() to avoid filling relationship fields
        
        // Fill only actual database columns
        $this->record->name = $this->data['name'] ?? null;
        $this->record->slug = $this->data['slug'] ?? null;
        $this->record->alt_text = $this->data['alt_text'] ?? $this->data['alt_text_seo'] ?? null;
        $this->record->description = $this->data['description'] ?? null;
        $this->record->colour = $this->data['colour'] ?? null;
        $this->record->condition = $this->data['condition'] ?? null;
        $this->record->specification = $this->data['specification'] ?? null;
        $this->record->bolt_pattern = $this->data['bolt_pattern'] ?? null;
        $this->record->offsets = $this->data['offsets'] ?? null;
        $this->record->centre_caps = $this->data['centre_caps'] ?? null;
        $this->record->item_code = $this->data['item_code'] ?? null;
        $this->record->sku = $this->data['sku'] ?? null;
        $this->record->warranty = $this->data['warranty'] ?? null;
        $this->record->weight = $this->data['weight'] ?? null;
        $this->record->regular_price = $this->data['regular_price'] ?? null;
        $this->record->discount_percent = $this->data['discount_percent'] ?? null;
        
        // Calculate discounted price only if both price and discount are provided
        $regular = (float)($this->data['regular_price'] ?? 0);
        $discount = (float)($this->data['discount_percent'] ?? 0);
        
        if ($regular > 0 && $discount > 0) {
            $this->record->discounted_price = round($regular - ($regular * ($discount / 100)), 2);
        } else {
            $this->record->discounted_price = $this->data['discounted_price'] ?? null;
        }
        // Don't auto-set discounted_price to regular price - only save what's imported
        $this->record->meta_description = $this->data['meta_description'] ?? null;
        
        \Log::info('RimImporter fillRecord', [
            'description' => $this->data['description'] ?? 'missing',
            'filled_description' => $this->record->description ?? 'not_set'
        ]);

        if (empty($this->record->slug) && !empty($this->record->name)) {
            $this->record->slug = Str::slug($this->record->name);
        }


        $this->record->is_set_of_4 = $this->parseBool($this->data['is_set_of_4'] ?? false);

        // Handle relationships in fillRecord since handleRecord is not called
        $this->whenFilled('rimBrand', function ($name) {
            $brand = $this->firstOrCreateByName(RimBrand::class, $name);
            if ($brand) $this->record->rim_brand_id = $brand->id;
        });

        $this->whenFilled('rimSize', function ($size) {
            $rimSize = RimSize::firstOrCreate(['size' => trim($size)]);
            if ($rimSize) $this->record->rim_size_id = $rimSize->id;
        });

        // Store rim attributes for later processing in saveRecord
        $this->rimAttributesToSync = [];
        $this->whenFilled('rimAttribute', function ($name) {
            // Get default car make and model for rim attributes
            $defaultMake = \App\Models\CarMake::first();
            $defaultModel = $defaultMake ? \App\Models\CarModel::where('car_make_id', $defaultMake->id)->first() : null;
            
            if ($defaultMake && $defaultModel) {
                // Split multiple attributes by semicolon or comma
                $attributeNames = preg_split('/[;,]/', $name);
                foreach ($attributeNames as $attrName) {
                    $attrName = trim($attrName);
                    if ($attrName) {
                        $attr = RimAttribute::firstOrCreate(
                            ['name' => $attrName],
                            [
                                'car_make_id' => $defaultMake->id,
                                'car_model_id' => $defaultModel->id,
                                'model_year' => date('Y')
                            ]
                        );
                        if ($attr) $this->rimAttributesToSync[] = $attr->id;
                    }
                }
            }
        });

        $this->whenFilled('rimCountry', function ($name) {
            $country = $this->firstOrCreateByName(RimCountry::class, $name);
            if ($country) $this->record->rim_country_id = $country->id;
        });
    }



    public function saveRecord(): void
    {
        $this->record->save();
        
        // Sync rim attributes using many-to-many relationship
        if (!empty($this->rimAttributesToSync)) {
            $this->record->attributes()->sync($this->rimAttributesToSync);
        }
        
        // Import feature image
        if (!empty($this->data['product_image_url'])) {
            $url = trim($this->data['product_image_url']);
            \Log::info('RimImporter: Attempting to import image', ['url' => $url, 'rim_id' => $this->record->id]);
            
            try {
                set_time_limit(60);
                
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    // Download image content first
                    $imageContent = file_get_contents($url);
                    if ($imageContent !== false) {
                        $tempFile = tempnam(sys_get_temp_dir(), 'rim_image_');
                        file_put_contents($tempFile, $imageContent);
                        
                        $media = $this->record->addMedia($tempFile)
                            ->usingName(basename(parse_url($url, PHP_URL_PATH)))
                            ->toMediaCollection('rim_feature_image');
                            
                        // Force conversion processing
                        $media->refresh();
                            
                        unlink($tempFile);
                        \Log::info('RimImporter: Successfully imported image', ['url' => $url, 'rim_id' => $this->record->id, 'media_id' => $media->id]);
                    } else {
                        \Log::warning('RimImporter: Failed to download image content', ['url' => $url, 'rim_id' => $this->record->id]);
                    }
                } else {
                    \Log::warning('RimImporter: Invalid image URL', ['url' => $url, 'rim_id' => $this->record->id]);
                }
            } catch (\Exception $e) {
                \Log::error('RimImporter: Failed to import image', ['url' => $url, 'rim_id' => $this->record->id, 'error' => $e->getMessage()]);
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Rims import completed. ' . number_format($import->successful_rows) . ' '
            . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' failed.';
        }

        return $body;
    }
}
