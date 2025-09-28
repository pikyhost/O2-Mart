<?php

namespace App\Filament\Resources\AutoPartCountryResource\Pages;

use App\Filament\Resources\AutoPartCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoPartCountry extends EditRecord
{
    protected static string $resource = AutoPartCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
