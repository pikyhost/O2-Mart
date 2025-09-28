<?php

namespace App\Exports;

use App\Models\AutoPart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AutoPartExporter implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AutoPart::select(
            'name',
            'parent_category_name',
            'sub_category_name',
            'brand_name',
            'country_name',
            'sku',
            'price_including_vat',
            'discount_percentage',
            'discounted_price',
            'weight',
            'height',
            'width',
            'length',
            'viscosity_grade',
            'details',
            'description',
            'photo_alt_text',
            'photo_link'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Sub_Category',
            'Brand',
            'Country',
            'SKU',
            'Price Includin VAT',
            'Discount Percentage',
            'Discounted prices',
            'Weight (KG)',
            'Height (CM)',
            'width (CM)',
            'Length (CM)',
            'Viscosity Grade',
            'Details',
            'Description',
            'Photo Alt text',
            'Photo Link',
        ];
    }
}
