<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Override media URL generation to use correct domain
        Media::macro('getUrl', function ($conversionName = '') {
            $relativePath = $this->id . '/' . $this->file_name;
            
            // Use current request domain or fallback to config
            if (request()->getHost() && request()->getHost() !== 'localhost') {
                $baseUrl = request()->getScheme() . '://' . request()->getHost();
            } else {
                $baseUrl = config('app.url');
            }
            
            return $baseUrl . '/storage/' . $relativePath;
        });
    }
}