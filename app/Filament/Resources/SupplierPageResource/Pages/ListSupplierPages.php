<?php

namespace App\Filament\Resources\SupplierPageResource\Pages;

use App\Filament\Resources\SupplierPageResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListSupplierPages extends BaseListPage
{
    protected static string $resource = SupplierPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
