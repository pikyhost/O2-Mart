<?php

namespace App\Filament\Resources\InstallerShopResource\Pages;

use App\Filament\Resources\InstallerShopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstallerShops extends ListRecords
{
    protected static string $resource = InstallerShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
