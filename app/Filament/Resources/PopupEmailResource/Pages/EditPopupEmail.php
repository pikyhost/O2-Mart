<?php

namespace App\Filament\Resources\PopupEmailResource\Pages;

use App\Filament\Resources\PopupEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPopupEmail extends EditRecord
{
    protected static string $resource = PopupEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
