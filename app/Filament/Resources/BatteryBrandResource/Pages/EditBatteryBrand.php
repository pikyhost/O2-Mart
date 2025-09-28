<?php

namespace App\Filament\Resources\BatteryBrandResource\Pages;

use App\Filament\Resources\BatteryBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatteryBrand extends EditRecord
{
    protected static string $resource = BatteryBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
