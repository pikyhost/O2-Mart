<?php

namespace App\Filament\Resources\VinRecordResource\Pages;

use App\Filament\Resources\VinRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVinRecords extends ListRecords
{
    protected static string $resource = VinRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
