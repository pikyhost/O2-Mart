<?php

namespace App\Filament\Resources\RimBrandResource\Pages;

use App\Filament\Resources\RimBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRimBrands extends ListRecords
{
    protected static string $resource = RimBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
