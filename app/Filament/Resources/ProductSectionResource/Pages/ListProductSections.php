<?php

namespace App\Filament\Resources\ProductSectionResource\Pages;

use App\Filament\Resources\ProductSectionResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListProductSections extends BaseListPage
{
    protected static string $resource = ProductSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
