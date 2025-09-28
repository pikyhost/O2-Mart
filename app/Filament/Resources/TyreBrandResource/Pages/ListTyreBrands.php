<?php

namespace App\Filament\Resources\TyreBrandResource\Pages;

use App\Filament\Resources\TyreBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTyreBrands extends ListRecords
{
    protected static string $resource = TyreBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
