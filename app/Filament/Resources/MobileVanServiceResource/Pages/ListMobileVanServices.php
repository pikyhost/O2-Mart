<?php

namespace App\Filament\Resources\MobileVanServiceResource\Pages;

use App\Filament\Resources\MobileVanServiceResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListMobileVanServices extends BaseListPage
{
    protected static string $resource = MobileVanServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
