<?php

namespace App\Filament\Resources\RimResource\Pages;

use App\Filament\Resources\RimResource;
use App\Filament\Resources\Pages\BaseListPage;
use Filament\Actions;

class ListRims extends BaseListPage
{
    protected static string $resource = RimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
