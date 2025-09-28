<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function uploadMultiple(array $files, string $directory): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $this->upload($file, $directory);
        }

        return $paths;
    }

    public function upload(UploadedFile $file, string $directory): string
    {
        $filename = $this->generateFileName($file);

        return $file->storeAs($directory, $filename, 'public');
    }

    public function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function generateFileName(UploadedFile $file): string
    {
        return time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    }
}
