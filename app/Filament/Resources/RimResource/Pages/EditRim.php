<?php

namespace App\Filament\Resources\RimResource\Pages;

use App\Filament\Resources\RimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRim extends EditRecord
{
    protected static string $resource = RimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
