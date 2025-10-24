<?php

namespace App\Filament\Resources\RimSizeResource\Pages;

use App\Filament\Resources\RimSizeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListRimSizes extends BaseListPage
{
    protected static string $resource = RimSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
