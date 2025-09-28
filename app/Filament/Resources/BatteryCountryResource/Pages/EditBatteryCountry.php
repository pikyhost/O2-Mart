<?php

namespace App\Filament\Resources\BatteryCountryResource\Pages;

use App\Filament\Resources\BatteryCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatteryCountry extends EditRecord
{
    protected static string $resource = BatteryCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
