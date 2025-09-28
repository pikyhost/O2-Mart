<?php

namespace App\Filament\Resources\ProductReviewResource\Pages;

use App\Filament\Resources\ProductReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductReview extends CreateRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $map = [
            'auto_part' => \App\Models\AutoPart::class,
            'tyre'      => \App\Models\Tyre::class,
            'battery'   => \App\Models\Battery::class,
            'rim'       => \App\Models\Rim::class,
        ];

        // رجّع قيمة الـ dropdown المساعدة من الـ class المخزّن
        $data['product_type_short'] = array_search($data['product_type'] ?? null, $map, true) ?: null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $map = [
            'auto_part' => \App\Models\AutoPart::class,
            'tyre'      => \App\Models\Tyre::class,
            'battery'   => \App\Models\Battery::class,
            'rim'       => \App\Models\Rim::class,
        ];

        if (isset($data['product_type_short'])) {
            $data['product_type'] = $map[$data['product_type_short']] ?? ($data['product_type'] ?? null);
            unset($data['product_type_short']);
        }

        return $data;
    }
}
