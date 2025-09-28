<?php

namespace App\Filament\Resources\AutoPartBrandResource\Pages;

use App\Filament\Resources\AutoPartBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutoPartBrands extends ListRecords
{
    protected static string $resource = AutoPartBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
