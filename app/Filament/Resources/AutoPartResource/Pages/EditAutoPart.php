<?php

namespace App\Filament\Resources\AutoPartResource\Pages;

use App\Filament\Resources\AutoPartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoPart extends EditRecord
{
    protected static string $resource = AutoPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
