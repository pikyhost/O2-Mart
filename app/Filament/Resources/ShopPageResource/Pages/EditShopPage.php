<?php

namespace App\Filament\Resources\ShopPageResource\Pages;

use App\Filament\Resources\ShopPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditShopPage extends EditRecord
{
    protected static string $resource = ShopPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }


}
