<?php

namespace App\Filament\Resources\MobileVanServiceResource\Pages;

use App\Filament\Resources\MobileVanServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMobileVanService extends EditRecord
{
    protected static string $resource = MobileVanServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
