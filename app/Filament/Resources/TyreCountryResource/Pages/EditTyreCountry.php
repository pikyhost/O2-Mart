<?php

namespace App\Filament\Resources\TyreCountryResource\Pages;

use App\Filament\Resources\TyreCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTyreCountry extends EditRecord
{
    protected static string $resource = TyreCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
