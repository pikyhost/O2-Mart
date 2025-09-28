<?php

namespace App\Filament\Resources\ShippingSettingResource\Pages;

use App\Filament\Resources\ShippingSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingSettings extends ListRecords
{
    protected static string $resource = ShippingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
