<?php

namespace App\Filament\Resources\RimBrandResource\Pages;

use App\Filament\Resources\RimBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRimBrand extends EditRecord
{
    protected static string $resource = RimBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
