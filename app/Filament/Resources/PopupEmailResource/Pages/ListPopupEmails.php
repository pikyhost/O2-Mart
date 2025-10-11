<?php

namespace App\Filament\Resources\PopupEmailResource\Pages;

use App\Filament\Resources\PopupEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPopupEmails extends ListRecords
{
    protected static string $resource = PopupEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
