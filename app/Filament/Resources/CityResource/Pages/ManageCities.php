<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Imports\CityImporter;
use App\Filament\Resources\CityResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseManagePage;

class ManageCities extends BaseManagePage
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(CityImporter::class),
        ];
    }
}
