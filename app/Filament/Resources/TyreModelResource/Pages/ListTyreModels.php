<?php

namespace App\Filament\Resources\TyreModelResource\Pages;

use App\Filament\Resources\TyreModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTyreModels extends ListRecords
{
    protected static string $resource = TyreModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
