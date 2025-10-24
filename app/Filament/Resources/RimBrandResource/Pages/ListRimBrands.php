<?php

namespace App\Filament\Resources\RimBrandResource\Pages;

use App\Filament\Resources\RimBrandResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListRimBrands extends BaseListPage
{
    protected static string $resource = RimBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
