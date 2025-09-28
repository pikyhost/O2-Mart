<?php

namespace App\Filament\Resources\WorkingHourResource\Pages;

use App\Filament\Resources\WorkingHourResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkingHours extends ManageRecords
{
    protected static string $resource = WorkingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
