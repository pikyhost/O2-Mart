<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\CarMake;
use App\Models\CarModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ProductImporter extends Importer implements WithChunkReading, WithBatchInserts
{
    protected static ?string $model = Product::class;

    public function __construct(Import $import, array $columnMap, array $options)
    {
        parent::__construct($import, $columnMap, $options);
        set_time_limit(3600);
        ini_set('memory_limit', '512M');
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('product_type')
                ->label('Product Type')
                ->requiredMapping()
                ->rules(['required', 'string', 'in:auto_parts,batteries,tyres'])
                ->example('auto_parts'),

            ImportColumn::make('name')
                ->label('Product Name/Title')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Car Battery 12V / Brake Oil / Michelin Tyre'),

            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255', 'unique:products,sku']),

            ImportColumn::make('item_code')
                ->label('Item Code')
                ->rules(['nullable', 'string', 'max:255', 'unique:products,item_code']),

            ImportColumn::make('description')
                ->label('Description/Details')
                ->rules(['nullable', 'string']),

            ImportColumn::make('parent_category_name')
                ->label('Main Category')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Batteries'),

            ImportColumn::make('sub_category_name')
                ->label('Sub Category')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Car Batteries'),

            ImportColumn::make('brand_name')
                ->label('Brand')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Michelin'),

            ImportColumn::make('country_name')
                ->label('Country of Origin')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('France'),

            ImportColumn::make('model')
                ->label('Model/Version')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Primacy 4'),

            ImportColumn::make('weight')
                ->label('Weight (KG)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('height')
                ->label('Height (CM)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('width')
                ->label('Width (CM)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('length')
                ->label('Length (CM)')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('dimensions')
                ->label('Dimensions (Alternative Format)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('200x150x180mm'),

            ImportColumn::make('regular_price')
                ->label('Regular Price')
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('discount_percentage')
                ->label('Discount Percentage')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:100']),

            ImportColumn::make('discounted_price')
                ->label('Discounted Price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('image_url')
                ->label('Image URL')
                ->rules(['nullable', 'string', 'max:500']),

            ImportColumn::make('image_alt_text')
                ->label('Image Alt Text')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('viscosity_grade')
                ->label('Viscosity Grade (Oil Products)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('5W-30'),

            ImportColumn::make('warranty')
                ->label('Warranty (Batteries/Tyres)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('24 months'),

            ImportColumn::make('capacity')
                ->label('Battery Capacity')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('75Ah'),

            ImportColumn::make('tire_size')
                ->label('Tire Size')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('215/55R17'),

            ImportColumn::make('wheel_diameter')
                ->label('Wheel Diameter')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('17'),

            ImportColumn::make('load_index')
                ->label('Load Index')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('94'),

            ImportColumn::make('speed_rating')
                ->label('Speed Rating')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('V'),

            ImportColumn::make('production_year')
                ->label('Production Year')
                ->numeric()
                ->rules(['nullable', 'integer', 'digits:4', 'min:2000']),

            ImportColumn::make('tyre_oem')
                ->label('OEM Tyre (Yes/No)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Yes'),

            ImportColumn::make('car_make')
                ->label('Compatible Car Make')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Toyota'),

            ImportColumn::make('car_model')
                ->label('Compatible Car Model')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Camry'),

            ImportColumn::make('car_year')
                ->label('Compatible Car Year')
                ->numeric()
                ->rules(['nullable', 'integer', 'between:1900,' . now()->year])
                ->example('2020'),

            ImportColumn::make('engine_performance')
                ->label('Engine Performance')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('2.5L 4-cylinder'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $validProductTypes = ['auto_parts', 'batteries', 'tyres'];
        $productType = $this->data['product_type'] ?? 'auto_parts';

        if (!in_array($productType, $validProductTypes)) {
            $productType = 'auto_parts';
        }

        // First try to find existing product by SKU or item_code
        $existingProduct = null;
        if (isset($this->data['sku'])) {
            $existingProduct = Product::where('sku', $this->data['sku'])->first();
        }

        if (!$existingProduct && isset($this->data['item_code'])) {
            $existingProduct = Product::where('item_code', $this->data['item_code'])->first();
        }

        // If product exists, update it instead of creating new one
        if ($existingProduct) {
            return $this->updateExistingProduct($existingProduct);
        }

        // Generate unique slug for new product
        $nameForSlug = $this->data['name'] ?? $this->getFallbackValue(['Title', 'Product Name'], 'product');
        $slug = $this->generateUniqueSlug($nameForSlug);

        // Handle relationships
        $category = $this->handleCategory();
        $brand = $this->handleBrand();
        $country = $this->handleCountry();
        $carModel = $this->handleCarModel();

        // Create product data array
        $productData = [
            'product_type' => $productType,
            'slug' => $slug,
            'name' => $this->data['name'] ?? $this->getFallbackValue(['Title', 'Product Name']),
            'sku' => $this->data['sku'] ?? $this->getFallbackValue(['SKU']),
            'item_code' => $this->data['item_code'] ?? null,
            'description' => $this->data['description'] ?? $this->getFallbackValue(['Description', 'Product Full Description']),
            'model' => $this->data['model'] ?? null,
            'parent_category_name' => $this->data['parent_category_name'] ?? $this->getFallbackValue(['Category', 'Product Category']),
            'sub_category_name' => $this->data['sub_category_name'] ?? $this->getFallbackValue(['Sub_Category', 'Product SubCategory']),
            'brand_name' => $this->data['brand_name'] ?? $this->getFallbackValue(['Brand']),
            'country_name' => $this->data['country_name'] ?? $this->getFallbackValue(['Country', 'Country of Origin']),
            'weight' => $this->sanitizeNumeric($this->data['weight'] ?? $this->getFallbackValue(['Weight (KG)', 'Weight (Kg)', 'weight_kg'])),
            'height' => $this->sanitizeNumeric($this->data['height'] ?? null),
            'width' => $this->sanitizeNumeric($this->data['width'] ?? null),
            'length' => $this->sanitizeNumeric($this->data['length'] ?? null),
            'dimensions' => $this->data['dimensions'] ?? $this->getFallbackValue(['Battery Dimensions (mm)']),
            'regular_price' => $this->sanitizeNumeric($this->data['regular_price'] ?? $this->getFallbackValue(['Price VAT Inclusive + Fitting cost', 'price_including_vat', 'price_vat_inclusive', 'Price Includin VAT'])),
            'discount_percentage' => $this->sanitizeNumeric($this->data['discount_percentage'] ?? null),
            'discounted_price' => $this->sanitizeNumeric($this->data['discounted_price'] ?? $this->getFallbackValue(['Discounted prices'])),
            'image_url' => $this->data['image_url'] ?? $this->getFallbackValue(['photo_link', 'Photo Link', 'Product Image']),
            'image_alt_text' => $this->data['image_alt_text'] ?? $this->getFallbackValue(['photo_alt_text', 'Photo Alt Text']),
            'category_id' => $category?->id,
            'brand_id' => $brand?->id,
            'country_id' => $country?->id,
            'car_model_id' => $carModel?->id,
            'car_make' => $this->data['car_make'] ?? $this->getFallbackValue(['Compatible Car Make']),
            'car_model' => $this->data['car_model'] ?? $this->getFallbackValue(['Compatible Car Model']),
            'car_year' => $this->data['car_year'] ?? null,
            'engine_performance' => $this->data['engine_performance'] ?? null,
        ];

        // Add product type specific fields
        switch ($productType) {
            case 'auto_parts':
                $productData['viscosity_grade'] = $this->data['viscosity_grade'] ?? null;
                break;

            case 'batteries':
                $productData['warranty'] = $this->data['warranty'] ?? $this->getFallbackValue(['Warranty']);
                $productData['capacity'] = $this->data['capacity'] ?? $this->getFallbackValue(['Battery Capacity']);
                break;

            case 'tyres':
                $productData['warranty'] = $this->data['warranty'] ?? $this->getFallbackValue(['Warranty (From Production year)']);
                $productData['tire_size'] = $this->data['tire_size'] ?? $this->getFallbackValue(['Tire Size']);
                $productData['wheel_diameter'] = $this->data['wheel_diameter'] ?? $this->getFallbackValue(['Wheel Diameter']);
                $productData['load_index'] = $this->data['load_index'] ?? $this->getFallbackValue(['Load Index']);
                $productData['speed_rating'] = $this->data['speed_rating'] ?? $this->getFallbackValue(['Speed Rating']);
                $productData['production_year'] = $this->data['production_year'] ?? null;
                $productData['tyre_oem'] = $this->data['tyre_oem'] ?? null;
                break;
        }

        return new Product($productData);
    }

    protected function updateExistingProduct(Product $existingProduct): Product
    {
        $updatableFields = [
            'name', 'description', 'model', 'regular_price', 'discount_percentage',
            'discounted_price', 'image_url', 'image_alt_text', 'viscosity_grade',
            'warranty', 'capacity', 'tire_size', 'wheel_diameter', 'load_index',
            'speed_rating', 'production_year', 'tyre_oem', 'car_make', 'car_model',
            'car_year', 'engine_performance', 'parent_category_name', 'sub_category_name',
            'brand_name', 'country_name', 'weight', 'height', 'width', 'length', 'dimensions'
        ];

        foreach ($updatableFields as $field) {
            if (isset($this->data[$field])) {
                $existingProduct->$field = $this->data[$field];
            }
        }

        if (isset($this->data['parent_category_name']) || isset($this->data['sub_category_name'])) {
            $category = $this->handleCategory();
            $existingProduct->category_id = $category?->id;
        }

        if (isset($this->data['brand_name'])) {
            $brand = $this->handleBrand();
            $existingProduct->brand_id = $brand?->id;
        }

        if (isset($this->data['country_name'])) {
            $country = $this->handleCountry();
            $existingProduct->country_id = $country?->id;
        }

        if ((isset($this->data['car_make']) || isset($this->data['car_model']))) {
            $carModel = $this->handleCarModel();
            $existingProduct->car_model_id = $carModel?->id;
        }

        return $existingProduct;
    }

    protected function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            if ($counter > 100) {
                $slug = $baseSlug . '-' . uniqid();
                break;
            }
        }

        return $slug;
    }

    protected function getFallbackValue(array $possibleKeys, $default = null)
    {
        foreach ($possibleKeys as $key) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
        }
        return $default;
    }

    protected function handleCategory(): ?Category
    {
        $parentCategoryName = $this->data['parent_category_name'] ??
            $this->getFallbackValue(['Category', 'Product Category']);

        $subCategoryName = $this->data['sub_category_name'] ??
            $this->getFallbackValue(['Sub_Category', 'Product SubCategory']);

        if (!$parentCategoryName) {
            return null;
        }

        $parentCategory = Category::updateOrCreate(
            ['name' => $parentCategoryName],
            ['name' => $parentCategoryName]
        );

        if (!$subCategoryName) {
            return $parentCategory;
        }

        return Category::updateOrCreate(
            ['name' => $subCategoryName, 'parent_id' => $parentCategory->id],
            ['name' => $subCategoryName, 'parent_id' => $parentCategory->id]
        );
    }

    protected function handleBrand(): ?Brand
    {
        $brandName = $this->data['brand_name'] ?? $this->getFallbackValue(['Brand']);
        return $brandName ? Brand::updateOrCreate(['name' => $brandName], ['name' => $brandName]) : null;
    }

    protected function handleCountry(): ?Country
    {
        $countryName = $this->data['country_name'] ?? $this->getFallbackValue(['Country', 'Country of Origin', 'Country Of Origin']);

        if (!$countryName) {
            return null;
        }

        $country = Country::where('name', $countryName)->first();
        if ($country) {
            return $country;
        }

        $countryCode = strtoupper(Str::substr($countryName, 0, 2));
        return Country::updateOrCreate(
            ['code' => $countryCode],
            ['name' => $countryName, 'code' => $countryCode]
        );
    }

    protected function handleCarModel(): ?CarModel
    {
        $carMakeName = $this->data['car_make'] ?? null;
        $modelName = $this->data['car_model'] ?? null;

        if (!$carMakeName || !$modelName) {
            return null;
        }

        $carMake = CarMake::updateOrCreate(
            ['name' => $carMakeName],
            ['name' => $carMakeName]
        );

        $carModel = CarModel::firstOrNew([
            'name' => $modelName,
            'car_make_id' => $carMake->id
        ]);

        if (!$carModel->exists) {
            $baseSlug = Str::slug($modelName);
            $slug = $baseSlug;
            $counter = 1;

            while (CarModel::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $carModel->slug = $slug;
            $carModel->save();
        }

        return $carModel;
    }

    protected function sanitizeNumeric($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (float) $value : null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
