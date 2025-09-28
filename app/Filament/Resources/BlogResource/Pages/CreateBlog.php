<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = auth()->id();

        return $data;
    }

    public function saveUploadedFileAttachmentAndGetUrl($file): ?string
    {
        $path = $file->store('blogs/content', 'public');
        return $path ? asset('storage/' . $path) : null;

    }
}
