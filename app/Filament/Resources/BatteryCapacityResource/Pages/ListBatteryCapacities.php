<?php

namespace App\Filament\Resources\BatteryCapacityResource\Pages;

use App\Filament\Resources\BatteryCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatteryCapacities extends ListRecords
{
    protected static string $resource = BatteryCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
