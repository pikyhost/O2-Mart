<?php

namespace App\Filament\Resources\CarTyreSpecResource\Pages;

use App\Filament\Resources\CarTyreSpecResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarTyreSpec extends EditRecord
{
    protected static string $resource = CarTyreSpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
