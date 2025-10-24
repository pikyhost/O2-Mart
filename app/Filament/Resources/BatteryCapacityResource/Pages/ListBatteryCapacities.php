<?php

namespace App\Filament\Resources\BatteryCapacityResource\Pages;

use App\Filament\Resources\BatteryCapacityResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBatteryCapacities extends BaseListPage
{
    protected static string $resource = BatteryCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
