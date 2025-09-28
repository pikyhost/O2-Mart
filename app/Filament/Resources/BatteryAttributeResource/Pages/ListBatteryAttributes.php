<?php

namespace App\Filament\Resources\BatteryAttributeResource\Pages;

use App\Filament\Resources\BatteryAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatteryAttributes extends ListRecords
{
    protected static string $resource = BatteryAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
