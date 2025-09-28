<?php

namespace App\Filament\Resources\BatteryDimensionResource\Pages;

use App\Filament\Resources\BatteryDimensionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatteryDimension extends EditRecord
{
    protected static string $resource = BatteryDimensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
