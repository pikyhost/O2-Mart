<?php

namespace App\Filament\Resources\CarTyreSpecResource\Pages;

use App\Filament\Resources\CarTyreSpecResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListCarTyreSpecs extends BaseListPage
{
    protected static string $resource = CarTyreSpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
