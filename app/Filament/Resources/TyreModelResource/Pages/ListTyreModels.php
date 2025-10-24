<?php

namespace App\Filament\Resources\TyreModelResource\Pages;

use App\Filament\Resources\TyreModelResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListTyreModels extends BaseListPage
{
    protected static string $resource = TyreModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
