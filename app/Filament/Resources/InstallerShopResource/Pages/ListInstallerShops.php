<?php

namespace App\Filament\Resources\InstallerShopResource\Pages;

use App\Filament\Resources\InstallerShopResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListInstallerShops extends BaseListPage
{
    protected static string $resource = InstallerShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
