<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Pages\BaseListPage;

class ListSettings extends BaseListPage
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
