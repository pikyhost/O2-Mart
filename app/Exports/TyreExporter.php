<?php

namespace App\Filament\Exports;

use App\Models\Tyre;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class TyreExporter extends Exporter
{
    protected static ?string $model = Tyre::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')->label('Title'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('brand.name')->label('Brand'),
            ExportColumn::make('tyreAttribute.tyre_attribute')->label('Tyre Attribute'),
            ExportColumn::make('width')->label('Width'),
            ExportColumn::make('height')->label('Height'),
            ExportColumn::make('wheel_diameter')->label('Wheel Diameter'),
            ExportColumn::make('model')->label('Model'),
            ExportColumn::make('load_index')->label('Load Index'),
            ExportColumn::make('speed_rating')->label('Speed Rating'),
            ExportColumn::make('weight_kg')->label('Weight (KG)'),
            ExportColumn::make('production_year')->label('Production Year'),
            ExportColumn::make('warranty')->label('Warranty'),
            ExportColumn::make('price_vat_inclusive')->label('Price VAT Inclusive + Fitting cost'),
            ExportColumn::make('discount_percentage')->label('Discount Percentage'),
            ExportColumn::make('discounted_price')->label('Discounted Price'),
            ExportColumn::make('sku')->label('SKU'),
            ExportColumn::make('alt_text')->label('Alt Text'),
            ExportColumn::make('image')->label('Image'),

            ExportColumn::make('tyreBrand.name')->label('Tyre Brand'),
            ExportColumn::make('tyreModel.name')->label('Tyre Model'),
            ExportColumn::make('tyreCountry.name')->label('Tyre Country'),
            ExportColumn::make('tyreSize.size')->label('Tyre Size'),
        ];
    }
}
