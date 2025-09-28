<?php

namespace App\Filament\Resources\RimSizeResource\Pages;

use App\Filament\Resources\RimSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRimSizes extends ListRecords
{
    protected static string $resource = RimSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
