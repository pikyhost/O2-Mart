<?php

namespace App\Filament\Resources\AutoPartBrandResource\Pages;

use App\Filament\Resources\AutoPartBrandResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListAutoPartBrands extends BaseListPage
{
    protected static string $resource = AutoPartBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
