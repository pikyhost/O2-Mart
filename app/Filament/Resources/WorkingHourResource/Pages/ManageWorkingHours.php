<?php

namespace App\Filament\Resources\WorkingHourResource\Pages;

use App\Filament\Resources\WorkingHourResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseManagePage;

class ManageWorkingHours extends BaseManagePage
{
    protected static string $resource = WorkingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
