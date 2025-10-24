<?php

namespace App\Filament\Resources\BatteryDimensionResource\Pages;

use App\Filament\Resources\BatteryDimensionResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBatteryDimensions extends BaseListPage
{
    protected static string $resource = BatteryDimensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
