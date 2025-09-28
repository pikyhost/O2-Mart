<?php

namespace App\Filament\Resources\RimCountryResource\Pages;

use App\Filament\Resources\RimCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRimCountries extends ListRecords
{
    protected static string $resource = RimCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
