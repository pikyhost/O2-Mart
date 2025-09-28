<?php

namespace App\Filament\Resources\ProductReviewResource\Pages;

use App\Filament\Resources\ProductReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductReviews extends ListRecords
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Review')
                ->mutateFormDataUsing(function (array $data): array {
                    $map = [
                        'auto_part' => \App\Models\AutoPart::class,
                        'tyre'      => \App\Models\Tyre::class,
                        'battery'   => \App\Models\Battery::class,
                        'rim'       => \App\Models\Rim::class,
                    ];

                    $data['product_type'] = $map[$data['product_type_short']] ?? null;
                    unset($data['product_type_short']);

                    return $data;
                })
                ->successNotificationTitle('Review created'),
        ];
    }
}
