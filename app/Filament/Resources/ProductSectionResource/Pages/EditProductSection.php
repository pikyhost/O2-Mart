<?php

namespace App\Filament\Resources\ProductSectionResource\Pages;

use App\Filament\Resources\ProductSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductSection extends EditRecord
{
    protected static string $resource = ProductSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
