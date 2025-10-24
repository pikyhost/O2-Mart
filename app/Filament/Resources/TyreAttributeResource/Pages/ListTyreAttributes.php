<?php

namespace App\Filament\Resources\TyreAttributeResource\Pages;

use App\Filament\Resources\TyreAttributeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListTyreAttributes extends BaseListPage
{
    protected static string $resource = TyreAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
