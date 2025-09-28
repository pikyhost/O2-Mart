<?php

namespace App\Filament\Resources\RimSizeResource\Pages;

use App\Filament\Resources\RimSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRimSize extends EditRecord
{
    protected static string $resource = RimSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
