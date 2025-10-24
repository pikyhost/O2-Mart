<?php

namespace App\Filament\Resources\TyreBrandResource\Pages;

use App\Filament\Resources\TyreBrandResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListTyreBrands extends BaseListPage
{
    protected static string $resource = TyreBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
