<?php

namespace App\Filament\Resources\PopupResource\Pages;

use App\Filament\Resources\PopupResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListPopups extends BaseListPage
{
    protected static string $resource = PopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
