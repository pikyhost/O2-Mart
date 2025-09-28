<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use App\Filament\Imports\AreaImporter;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAreas extends ManageRecords
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(AreaImporter::class),
        ];
    }
}
