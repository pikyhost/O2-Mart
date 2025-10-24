<?php

namespace App\Filament\Resources\ShopPageResource\Pages;

use App\Filament\Resources\ShopPageResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;
use Illuminate\Database\Eloquent\Model;

class ListShopPages extends BaseListPage
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
