<?php

namespace App\Filament\Resources\ShopPageResource\Pages;

use App\Filament\Resources\ShopPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListShopPages extends ListRecords
{
    protected static string $resource = ShopPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

}
