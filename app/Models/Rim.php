<?php

namespace App\Models;

use App\Traits\HasShareUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Cviebrock\EloquentSluggable\Sluggable;

class Rim extends Model implements HasMedia
{

    use HasFactory, InteractsWithMedia, Sluggable, HasShareUrl;

    protected $guarded = [];
    protected $appends = ['share_url', 'rim_feature_image_url', 'rim_secondary_image_url', 'rim_gallery_urls', 'feature_image_url', 'original_url', 'weight_kg', 'zoom_image_url'];
    protected $casts = [
        'buy_3_get_1_free' => 'boolean',
    ];


    protected function getShareType(): string
    {
        return 'Rims';

    }
    public function registerMediaCollections(): void
    {
        $imageMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/bmp',
            'image/svg+xml',
            'image/tiff',
        ];

        $this->addMediaCollection('rim_feature_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes)
            ->useDisk('public');

        $this->addMediaCollection('rim_secondary_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes)
            ->useDisk('public');

        $this->addMediaCollection('rim_gallery')
            ->acceptsMimeTypes(array_merge($imageMimeTypes, [
                'video/mp4',
                'video/mpeg',
                'video/quicktime',
            ]))
            ->useDisk('public');
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->nonQueued();
            
        $this->addMediaConversion('large')
            ->width(800)
            ->height(800)
            ->nonQueued();
    }







    public function attributes()
    {
        return $this->belongsToMany(\App\Models\RimAttribute::class, 'rim_attribute_rim');
    }

    public function rimAttribute()
    {
        return $this->belongsTo(\App\Models\RimAttribute::class);
    }

    public function rimBrand()
    {
        return $this->belongsTo(\App\Models\RimBrand::class);
    }

    public function rimSize()
    {
        return $this->belongsTo(\App\Models\RimSize::class);
    }

    public function rimCountry()
    {
        return $this->belongsTo(\App\Models\RimCountry::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRimFeatureImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('rim_feature_image');
        
        if (!$media) {
            return null;
        }
        
        // Always use original URL to ensure images show
        $url = $media->getUrl();
        
        if (empty($url)) {
            return null;
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = url($url);
        }
        
        return $url;
    }



    public function getRimSecondaryImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('rim_secondary_image') ?: null;
    }

    public function getRimGalleryUrlsAttribute(): array
    {
        return $this->getMedia('rim_gallery')
            ->map(fn($media) => $media->getUrl())
            ->toArray();
    }

    public function getFeatureImageUrlAttribute(): ?string
    {
        return $this->getRimFeatureImageUrlAttribute();
    }

    public function getOriginalUrlAttribute(): ?string
    {
        return $this->getRimFeatureImageUrlAttribute();
    }

    public function getWeightKgAttribute(): ?float
    {
        return $this->weight;
    }

    public function getZoomImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('rim_feature_image');
        if (!$media) {
            return null;
        }
        
        // Always try to return large conversion URL format
        // Frontend expects this specific URL pattern for zoom
        $originalUrl = $media->getUrl();
        $pathInfo = pathinfo($originalUrl);
        
        // Generate large conversion URL pattern
        $largeUrl = str_replace(
            '/' . $media->id . '/' . $media->file_name,
            '/' . $media->id . '/conversions/' . $pathInfo['filename'] . '-large.jpg',
            $originalUrl
        );
        
        return $largeUrl;
    }



    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name'],
        ];
    }

    //     public function getFeatureImageUrlAttribute()
    // {
    //     return $this->getFirstMediaUrl('rim_feature_image');
    // }

    public function reviews()
    {
        return $this->morphMany(ProductReview::class, 'product');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

    public static function fixImageIssues($rimId = null)
    {
        $query = $rimId ? self::where('id', $rimId) : self::query();
        $rims = $query->get();
        $fixed = 0;
        
        foreach ($rims as $rim) {
            $media = $rim->getFirstMedia('rim_feature_image');
            if ($media) {
                try {
                    // Regenerate conversions
                    $media->performConversions();
                    $fixed++;
                    \Log::info("Fixed image for rim ID {$rim->id}");
                } catch (\Exception $e) {
                    \Log::error("Failed to fix image for rim ID {$rim->id}: " . $e->getMessage());
                }
            } else {
                \Log::warning("No media found for rim ID {$rim->id}");
            }
        }
        
        \Log::info("Fixed images for {$fixed} rims");
        return $fixed;
    }

}
