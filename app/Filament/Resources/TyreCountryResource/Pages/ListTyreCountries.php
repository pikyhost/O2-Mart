<?php

namespace App\Filament\Resources\TyreCountryResource\Pages;

use App\Filament\Resources\TyreCountryResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListTyreCountries extends BaseListPage
{
    protected static string $resource = TyreCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
