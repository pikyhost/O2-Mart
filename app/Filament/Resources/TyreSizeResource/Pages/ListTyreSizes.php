<?php

namespace App\Filament\Resources\TyreSizeResource\Pages;

use App\Filament\Resources\TyreSizeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListTyreSizes extends BaseListPage
{
    protected static string $resource = TyreSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
