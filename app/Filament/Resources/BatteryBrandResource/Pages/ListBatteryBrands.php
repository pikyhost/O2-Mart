<?php

namespace App\Filament\Resources\BatteryBrandResource\Pages;

use App\Filament\Resources\BatteryBrandResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBatteryBrands extends BaseListPage
{
    protected static string $resource = BatteryBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
