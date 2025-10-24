<?php

namespace App\Filament\Resources\CarModelResource\Pages;

use App\Filament\Resources\CarModelResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListCarModels extends BaseListPage
{
    protected static string $resource = CarModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
