<?php

namespace App\Filament\Resources\BatteryResource\Pages;

use App\Filament\Resources\BatteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBatteries extends ListRecords
{
    protected static string $resource = BatteryResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->orderBy('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
