<?php

namespace App\Filament\Resources\TyreResource\Pages;

use App\Filament\Resources\TyreResource;
use App\Filament\Resources\Pages\BaseListPage;
use Filament\Actions;

class ListTyres extends BaseListPage
{
    protected static string $resource = TyreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
