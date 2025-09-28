<?php

namespace App\Filament\Resources\SupplierPageResource\Pages;

use App\Filament\Resources\SupplierPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierPages extends ListRecords
{
    protected static string $resource = SupplierPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
