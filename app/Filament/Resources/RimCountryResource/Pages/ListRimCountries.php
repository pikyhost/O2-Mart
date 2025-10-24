<?php

namespace App\Filament\Resources\RimCountryResource\Pages;

use App\Filament\Resources\RimCountryResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListRimCountries extends BaseListPage
{
    protected static string $resource = RimCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
