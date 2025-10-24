<?php

namespace App\Filament\Resources\BatteryAttributeResource\Pages;

use App\Filament\Resources\BatteryAttributeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBatteryAttributes extends BaseListPage
{
    protected static string $resource = BatteryAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
