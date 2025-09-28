<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     public function saveUploadedFileAttachmentAndGetUrl($file): ?string
    {
        $path = $file->store('blogs/content', 'public');

        if (! $path) {
            return null;
        }

        return $path ? asset('storage/' . $path) : null;

    }
}
