<?php

namespace App\Filament\Resources\BlogUserLikeResource\Pages;

use App\Filament\Resources\BlogUserLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogUserLike extends EditRecord
{
    protected static string $resource = BlogUserLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
