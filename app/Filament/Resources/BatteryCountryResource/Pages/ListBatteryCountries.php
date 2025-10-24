<?php

namespace App\Filament\Resources\BatteryCountryResource\Pages;

use App\Filament\Resources\BatteryCountryResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBatteryCountries extends BaseListPage
{
    protected static string $resource = BatteryCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
