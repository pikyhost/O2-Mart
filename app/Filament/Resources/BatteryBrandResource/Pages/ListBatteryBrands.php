<?php

namespace App\Filament\Resources\BatteryBrandResource\Pages;

use App\Filament\Resources\BatteryBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatteryBrands extends ListRecords
{
    protected static string $resource = BatteryBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
