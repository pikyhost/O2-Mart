<?php

namespace App\Filament\Resources\BatteryCapacityResource\Pages;

use App\Filament\Resources\BatteryCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatteryCapacity extends EditRecord
{
    protected static string $resource = BatteryCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
