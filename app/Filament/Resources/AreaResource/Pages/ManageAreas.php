<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use App\Filament\Imports\AreaImporter;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseManagePage;
use Illuminate\Database\Eloquent\Builder;

class ManageAreas extends BaseManagePage
{
    protected static string $resource = AreaResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->orderBy('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->label('Bulk Upload')
                ->importer(AreaImporter::class)
                ->chunkSize(10),
        ];
    }
}
