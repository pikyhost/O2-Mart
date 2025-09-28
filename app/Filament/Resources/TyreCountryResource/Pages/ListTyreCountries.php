<?php

namespace App\Filament\Resources\TyreCountryResource\Pages;

use App\Filament\Resources\TyreCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTyreCountries extends ListRecords
{
    protected static string $resource = TyreCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
