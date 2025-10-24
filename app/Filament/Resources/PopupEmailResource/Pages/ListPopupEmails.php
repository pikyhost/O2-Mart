<?php

namespace App\Filament\Resources\PopupEmailResource\Pages;

use App\Filament\Resources\PopupEmailResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListPopupEmails extends BaseListPage
{
    protected static string $resource = PopupEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
