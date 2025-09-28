<?php

namespace App\Filament\Resources\RimAttributeResource\Pages;

use App\Filament\Resources\RimAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRimAttributes extends ListRecords
{
    protected static string $resource = RimAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
