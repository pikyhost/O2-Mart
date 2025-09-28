<?php

namespace App\Filament\Resources\TyreResource\Pages;

use App\Filament\Resources\TyreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTyre extends ViewRecord
{
    protected static string $resource = TyreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
