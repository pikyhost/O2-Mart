<?php

namespace App\Filament\Resources\TyreSizeResource\Pages;

use App\Filament\Resources\TyreSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTyreSize extends EditRecord
{
    protected static string $resource = TyreSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
