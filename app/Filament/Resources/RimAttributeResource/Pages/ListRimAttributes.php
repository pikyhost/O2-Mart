<?php

namespace App\Filament\Resources\RimAttributeResource\Pages;

use App\Filament\Resources\RimAttributeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListRimAttributes extends BaseListPage
{
    protected static string $resource = RimAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
