<?php

namespace App\Filament\Resources\TyreAttributeResource\Pages;

use App\Filament\Resources\TyreAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTyreAttributes extends ListRecords
{
    protected static string $resource = TyreAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
