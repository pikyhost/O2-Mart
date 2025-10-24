<?php

namespace App\Filament\Resources\ShippingSettingResource\Pages;

use App\Filament\Resources\ShippingSettingResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListShippingSettings extends BaseListPage
{
    protected static string $resource = ShippingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
