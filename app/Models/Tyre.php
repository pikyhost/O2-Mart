<?php

namespace App\Models;

use App\Traits\HasShareUrl;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tyre extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Sluggable, HasShareUrl;

    protected $guarded = [];
    protected $appends = [
        'tyre_feature_image_url',
        'tyre_secondary_image_url',
        'tyre_gallery_urls',
        'share_url',
    ];
    protected $casts = [
        'buy_3_get_1_free' => 'boolean',
    ];


    protected function getShareType(): string
    {
        return 'Tyres';

    }
    /**
     * Media Collections (Images & Videos)
     */
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

        $this->addMediaCollection('tyre_feature_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('tyre_secondary_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('tyre_gallery')
            ->acceptsMimeTypes(array_merge($imageMimeTypes, [
                'video/mp4',
                'video/mpeg',
                'video/quicktime',
            ]));
    }

    /**
     * Relationships
     */
    public function tyreAttribute()
    {
        return $this->belongsTo(TyreAttribute::class);
    }

    public function tyreBrand()
    {
        return $this->belongsTo(TyreBrand::class);
    }

    public function tyreModel()
    {
        return $this->belongsTo(TyreModel::class);
    }

    public function tyreSize()
    {
        return $this->belongsTo(TyreSize::class);
    }

    public function tyreCountry()
    {
        return $this->belongsTo(TyreCountry::class);
    }

    /**
     * Slug settings
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title'],
        ];
    }

    /**
     * Use slug instead of ID in routes (optional)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessors for Media URLs
     */
//     public function getTyreFeatureImageUrl(): ?string
//     {
//         return $this->getFirstMediaUrl('tyre_feature_image') ?: null;
// }

//     public function getTyreSecondaryImageUrl(): ?string
//     {
//         return $this->getFirstMediaUrl('tyre_secondary_image') ?: null;
//     }

//     public function getTyreGalleryUrls(string $conversion = null): array
//     {
//         return $this->getMedia('tyre_gallery')
//             ->map(fn($media) => $conversion && $media->hasGeneratedConversion($conversion)
//                 ? $media->getUrl($conversion)
//                 : $media->getUrl())
//             ->toArray();
//     }

    public function getTyreFeatureImageUrlAttribute(): ?string
    {
        // First try media library
        $mediaUrl = $this->getFirstMediaUrl('tyre_feature_image');
        if (!empty($mediaUrl)) {
            return $mediaUrl;
        }

        // Then try legacy image field
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image); 
        }

        return null;
    }


    public function getTyreSecondaryImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('tyre_secondary_image');
    }

    public function getTyreGalleryUrlsAttribute(): array
    {
        return $this->getMedia('tyre_gallery')->map(fn($m) => $m->getUrl())->toArray();
    }

    public function reviews()
    {
        return $this->morphMany(ProductReview::class, 'product');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

}
