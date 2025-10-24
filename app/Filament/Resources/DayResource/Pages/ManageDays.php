<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseManagePage;

class ManageDays extends BaseManagePage
{
    protected static string $resource = DayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
