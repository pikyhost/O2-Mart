<?php

namespace App\Filament\Resources\VinRecordResource\Pages;

use App\Filament\Resources\VinRecordResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListVinRecords extends BaseListPage
{
    protected static string $resource = VinRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
