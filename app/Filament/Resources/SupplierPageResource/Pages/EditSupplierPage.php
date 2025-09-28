<?php

namespace App\Filament\Resources\SupplierPageResource\Pages;

use App\Filament\Resources\SupplierPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierPage extends EditRecord
{
    protected static string $resource = SupplierPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
