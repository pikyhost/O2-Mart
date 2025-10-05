<?php

namespace App\Models;

use App\Traits\HasShareUrl;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Battery extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Sluggable, HasShareUrl;
    

    protected $fillable = [
        'name','slug','item_code','sku','warranty','weight',
        'regular_price','discount_percentage','discounted_price',
        'description','battery_brand_id','capacity_id','dimension_id',
        'battery_country_id','country_id','category_id', 'meta_title',
        'meta_description', 'alt_text',
    ];
    protected $appends = ['image_url', 'gallery_images', 'feature_image_url', 'share_url'];
    protected $casts = [
        'buy_3_get_1_free' => 'boolean',
    ];


    protected function getShareType(): string
    {
        return 'Batteries';

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

        $this->addMediaCollection('battery_feature_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('battery_secondary_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('battery_gallery')
            ->acceptsMimeTypes(array_merge($imageMimeTypes, [
                'video/mp4',
                'video/mpeg',
                'video/quicktime',
            ]));
    }
    
    public function attributes()
    {
        return $this->belongsToMany(BatteryAttribute::class, 'battery_attribute_battery');
    }
    
    public function capacity()
    {
        return $this->belongsTo(BatteryCapacity::class, 'capacity_id');
    }
    
    public function dimension()
    {
        return $this->belongsTo(BatteryDimension::class, 'dimension_id');
    }
    
    public function batteryBrand()
    {
        return $this->belongsTo(BatteryBrand::class, 'battery_brand_id');
    }

    protected static function booted()
    {
        static::creating(function ($battery) {
            // Handle Category
            if ($battery->category_name) {
                $parentCategory = \App\Models\Category::firstOrCreate(
                    ['name' => $battery->category_name],
                    [
                        'slug' => \Cviebrock\EloquentSluggable\Services\SlugService::createSlug(\App\Models\Category::class, 'slug', $battery->category_name),
                        'is_published' => true,
                    ]
                );

                // Optional: Check for sub-category if using dynamic naming (e.g., a separator in the name)
                if (!empty($battery->sub_category_name)) {
                    $subCategory = \App\Models\Category::firstOrCreate(
                        [
                            'name' => $battery->sub_category_name,
                            'parent_id' => $parentCategory->id,
                        ],
                        [
                            'slug' => \Cviebrock\EloquentSluggable\Services\SlugService::createSlug(\App\Models\Category::class, 'slug', $battery->sub_category_name),
                            'is_published' => true,
                        ]
                    );

                    $battery->category_id = $subCategory->id;
                } else {
                    $battery->category_id = $parentCategory->id;
                }
            }



            // Handle Country
            if ($battery->country_name) {
                $country = \App\Models\Country::firstOrCreate(
                    ['name' => $battery->country_name],
                    ['code' => strtoupper(substr($battery->country_name, 0, 2))]
                );
                $battery->country_id = $country->id;
            }
        });
    }

    /**
     * Configure the settings for generating slugs.
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name'],
        ];
    }


    /**
     * Get the route key name for Laravel routing.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function batteryCountry()
    {
        return $this->belongsTo(BatteryCountry::class, 'battery_country_id');
    }

    public function getBatterySecondaryImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('battery_secondary_image') ?: null;
    }

    public function getBatteryGalleryUrls(string $conversion = null): array
    {
        return $this->getMedia('battery_gallery')
            ->map(fn($media) => $conversion && $media->hasGeneratedConversion($conversion) ? $media->getUrl($conversion) : $media->getUrl())
            ->toArray();
    }
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('battery_secondary_image') ?: null;
    }

    public function getGalleryImagesAttribute(): array
    {
        return $this->getMedia('battery_gallery')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function getFeatureImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('battery_feature_image');
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
