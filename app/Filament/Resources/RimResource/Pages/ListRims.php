<?php

namespace App\Filament\Resources\RimResource\Pages;

use App\Filament\Resources\RimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRims extends ListRecords
{
    protected static string $resource = RimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
