<?php

namespace App\Filament\Resources\CarTyreSpecResource\Pages;

use App\Filament\Resources\CarTyreSpecResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarTyreSpecs extends ListRecords
{
    protected static string $resource = CarTyreSpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
