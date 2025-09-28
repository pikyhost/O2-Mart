<?php

namespace App\Filament\Resources\BatteryDimensionResource\Pages;

use App\Filament\Resources\BatteryDimensionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatteryDimensions extends ListRecords
{
    protected static string $resource = BatteryDimensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
