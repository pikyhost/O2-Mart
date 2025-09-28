<?php

namespace App\Filament\Resources\CarMakeResource\Pages;

use App\Filament\Resources\CarMakeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarMake extends EditRecord
{
    protected static string $resource = CarMakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
