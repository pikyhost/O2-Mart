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
    protected $appends = ['share_url', 'rim_feature_image_url', 'rim_secondary_image_url', 'rim_gallery_urls'];


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
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('rim_secondary_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('rim_gallery')
            ->acceptsMimeTypes(array_merge($imageMimeTypes, [
                'video/mp4',
                'video/mpeg',
                'video/quicktime',
            ]));
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->quality(90)
            ->sharpen(5)
            ->nonQueued()
            ->performOnCollections('rim_feature_image', 'rim_secondary_image', 'rim_gallery');
            
        $this->addMediaConversion('large')
            ->width(1600)
            ->height(1600)
            ->quality(100)
            ->sharpen(3)
            ->nonQueued()
            ->performOnCollections('rim_feature_image', 'rim_secondary_image', 'rim_gallery');
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
        return $this->getFirstMediaUrl('rim_feature_image') ?: null;
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

}
