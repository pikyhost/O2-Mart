<?php

namespace App\Filament\Resources\RimAttributeResource\Pages;

use App\Filament\Resources\RimAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRimAttribute extends EditRecord
{
    protected static string $resource = RimAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
