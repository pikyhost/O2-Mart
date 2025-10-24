<?php

namespace App\Filament\Resources\CarMakeResource\Pages;

use App\Filament\Resources\CarMakeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListCarMakes extends BaseListPage
{
    protected static string $resource = CarMakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
