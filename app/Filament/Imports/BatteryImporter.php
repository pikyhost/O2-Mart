<?php

namespace App\Filament\Imports;

use App\Models\Battery;
use App\Models\BatteryAttribute;
use App\Models\BatteryBrand;
use App\Models\BatteryCapacity;
use App\Models\BatteryCountry;
use App\Models\BatteryDimension;
use App\Models\Category;
use App\Models\Country;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;


class BatteryImporter extends BaseUpsertImporter
{
    use ImportHelpers;

    protected static ?string $model = \App\Models\Battery::class;
    protected static array $uniqueBy = ['sku'];
    
    public static function getChunkSize(): int
    {
        return 50; // Process in smaller chunks
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required','max:255']),
            ImportColumn::make('slug')->rules(['nullable','max:255']),
            ImportColumn::make('item_code')->rules(['nullable','max:255']),
            ImportColumn::make('sku')->requiredMapping()->rules(['required','max:255']),

            ImportColumn::make('battery_brand')->label('Battery Brand'),
            ImportColumn::make('capacity')->label('Battery Capacity'),
            ImportColumn::make('dimension')->label('Battery Dimension'),
            ImportColumn::make('battery_country')->label('Battery Country'),
            ImportColumn::make('country')->label('Country Name'),

            ImportColumn::make('weight')->rules(['nullable','numeric']),
            ImportColumn::make('warranty')->rules(['nullable','max:255']),
            ImportColumn::make('regular_price')
                ->rules(['nullable','numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('discount_percentage')
                ->rules(['nullable','numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = str_replace(['%', ' '], '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('discounted_price')
                ->rules(['nullable','numeric'])
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '') return null;
                    $cleaned = preg_replace('/[^0-9.]/', '', (string)$state);
                    return is_numeric($cleaned) ? (float)$cleaned : null;
                }),
            ImportColumn::make('description'),

            ImportColumn::make('feature_image')->label('Feature Image URL'),
            ImportColumn::make('secondary_image')->label('Secondary Image URL'),
            ImportColumn::make('gallery_images')->label('Gallery Image URLs (comma separated)'),
            ImportColumn::make('battery_attributes')->label('Battery Attributes'),
            
            ImportColumn::make('meta_title')->rules(['nullable', 'max:255']),
            ImportColumn::make('meta_description')->rules(['nullable', 'max:500']),
            ImportColumn::make('alt_text')->rules(['nullable', 'max:255']),
        ];
    }



    public function fillRecord(): void
    {
        parent::fillRecord();

        $baseSlug = \Illuminate\Support\Str::slug(
            preg_replace('/\s+/', ' ', trim($this->record->slug ?? $this->record->name ?? ''))
        );
        
        // Ensure unique slug by checking existing records
        $slug = $baseSlug;
        $counter = 1;
        while (\App\Models\Battery::where('slug', $slug)->where('id', '!=', $this->record->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $this->record->slug = $slug;

        $regular = (float)($this->data['regular_price'] ?? 0);
        $discount = (float)($this->data['discount_percentage'] ?? 0);
        $importedDiscounted = $this->data['discounted_price'] ?? null;
        
        // Set regular price
        if ($regular > 0) {
            $this->record->regular_price = $regular;
        }
        
        // Use imported discounted price if provided, otherwise calculate if discount exists
        if (!empty($importedDiscounted)) {
            // Use the imported discounted price as-is
            $this->record->discounted_price = (float)$importedDiscounted;
        } elseif ($regular > 0 && $discount > 0) {
            // Calculate discounted price only if no imported price and discount exists
            $this->record->discounted_price = round($regular - ($regular * ($discount / 100)), 2);
        }

        if (($this->data['item_code'] ?? null) === '?') {
            $this->record->item_code = null;
        }

        // Handle warranty - set default if empty
        if (empty($this->record->warranty)) {
            $this->record->warranty = '12 months';
        }

        // Handle relationships
        $this->whenFilled('battery_brand', function ($val) {
            $brand = \App\Models\BatteryBrand::firstOrCreate(['value' => trim($val)]);
            $this->record->battery_brand_id = $brand->id;
        });

        $this->whenFilled('capacity', function ($val) {
            $cap = \App\Models\BatteryCapacity::firstOrCreate(['value' => trim($val)]);
            $this->record->capacity_id = $cap->id;
        });

        $this->whenFilled('dimension', function ($val) {
            $dim = \App\Models\BatteryDimension::firstOrCreate(['value' => trim($val)]);
            $this->record->dimension_id = $dim->id;
        });

        $this->whenFilled('battery_country', function ($val) {
            $code = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($val, 0, 2));
            $bc = \App\Models\BatteryCountry::firstOrCreate(['name' => trim($val)], ['code' => $code]);
            $this->record->battery_country_id = $bc->id;
        });

        $this->whenFilled('country', function ($val) {
            $code = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($val, 0, 2));
            $c = \App\Models\Country::firstOrCreate(['name' => trim($val)], ['code' => $code]);
            $this->record->country_id = $c->id;
        });
    }

    public function saveRecord(): void
    {
        $this->record->save();

        // Attributes
        if (!empty($this->data['battery_attributes'])) {
            $ids = [];
            // Get default car make and model (first available)
            $defaultMake = \App\Models\CarMake::first();
            $defaultModel = $defaultMake ? \App\Models\CarModel::where('car_make_id', $defaultMake->id)->first() : null;
            
            if ($defaultMake && $defaultModel) {
                foreach (array_map('trim', explode(';', (string)$this->data['battery_attributes'])) as $name) {
                    if ($name === '') continue;
                    $ids[] = \App\Models\BatteryAttribute::firstOrCreate(
                        ['name' => $name],
                        [
                            'car_make_id' => $defaultMake->id,
                            'car_model_id' => $defaultModel->id,
                            'model_year' => date('Y')
                        ]
                    )->id;
                }
                $this->record->attributes()->sync($ids);
            }
        }

        // Import feature image
        if (!empty($this->data['feature_image'])) {
            $url = trim($this->data['feature_image']);
            \Log::info('BatteryImporter: Attempting to import image', ['url' => $url, 'battery_id' => $this->record->id]);
            
            try {
                set_time_limit(60);
                
                // Validate URL format
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    \Log::warning('BatteryImporter: Invalid image URL format', ['url' => $url]);
                    return;
                }
                
                // Test if URL is accessible
                $headers = @get_headers($url);
                if (!$headers || strpos($headers[0], '200') === false) {
                    \Log::warning('BatteryImporter: Image URL not accessible', ['url' => $url, 'headers' => $headers[0] ?? 'No response']);
                    return;
                }
                
                $media = $this->record->addMediaFromUrl($url)
                    ->toMediaCollection('battery_feature_image');
                    
                \Log::info('BatteryImporter: Successfully imported image', [
                    'url' => $url, 
                    'battery_id' => $this->record->id,
                    'media_id' => $media->id,
                    'file_path' => $media->getPath()
                ]);
                
            } catch (\Exception $e) {
                \Log::error('BatteryImporter: Failed to import image', [
                    'url' => $url,
                    'battery_id' => $this->record->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public function import(): void
    {
        $this->record = $this->resolveRecord();
        $this->fillRecord();
        $this->saveRecord();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $ok = number_format($import->successful_rows);
        $ng = number_format($import->getFailedRowsCount());
        return "Batteries import completed: {$ok} imported, {$ng} failed.";
    }
}
