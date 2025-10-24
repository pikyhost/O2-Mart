<?php

namespace App\Filament\Resources\BlogUserLikeResource\Pages;

use App\Filament\Resources\BlogUserLikeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListBlogUserLikes extends BaseListPage
{
    protected static string $resource = BlogUserLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
