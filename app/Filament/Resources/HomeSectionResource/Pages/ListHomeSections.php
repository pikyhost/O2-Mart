<?php

namespace App\Filament\Resources\HomeSectionResource\Pages;

use App\Filament\Resources\HomeSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeSections extends ListRecords
{
    protected static string $resource = HomeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
