<?php

namespace App\Filament\Resources\MobileVanServiceResource\Pages;

use App\Filament\Resources\MobileVanServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMobileVanServices extends ListRecords
{
    protected static string $resource = MobileVanServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
