<?php

namespace App\Filament\Resources\AutoPartCountryResource\Pages;

use App\Filament\Resources\AutoPartCountryResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListAutoPartCountries extends BaseListPage
{
    protected static string $resource = AutoPartCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
