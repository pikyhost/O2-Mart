<?php

namespace App\Filament\Resources\AutoPartBrandResource\Pages;

use App\Filament\Resources\AutoPartBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoPartBrand extends EditRecord
{
    protected static string $resource = AutoPartBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
