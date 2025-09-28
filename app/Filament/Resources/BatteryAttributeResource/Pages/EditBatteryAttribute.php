<?php

namespace App\Filament\Resources\BatteryAttributeResource\Pages;

use App\Filament\Resources\BatteryAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatteryAttribute extends EditRecord
{
    protected static string $resource = BatteryAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
