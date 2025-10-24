<?php

namespace App\Filament\Resources\AutoPartResource\Pages;

use App\Filament\Resources\AutoPartResource;
use App\Filament\Resources\Pages\BaseListPage;
use Filament\Actions;

class ListAutoParts extends BaseListPage
{
    protected static string $resource = AutoPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
