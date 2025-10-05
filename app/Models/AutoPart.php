<?php

namespace App\Models;

use App\Traits\HasShareUrl;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;


class AutoPart extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Sluggable, HasShareUrl;

       protected $fillable = [
            'sku',
            'name',
            'slug',
            'price_including_vat',
            'discount_percentage',  
            'discounted_price', 
            'description',
            'details',
            'photo_alt_text',
            'weight',
            'height',
            'width',
            'length',
            'category_id',
            'auto_part_brand_id',
            'auto_part_country_id',
            'viscosity_grade_id',
            'meta_title',
            'meta_description',
            'alt_text',
        ];


    protected $casts = [
        'weight' => 'float',
        'height' => 'float',
        'width' => 'float',
        'length' => 'float',
        'price_including_vat' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'cross_reference_numbers' => 'array',
        'buy_3_get_1_free' => 'boolean',
    ];

    protected $appends = ['share_url'];

    protected function getShareType(): string
    {
        return 'autoparts';

    }

    protected static function booted()
    {
        static::creating(function ($autoPart) {
            // Handle Category
            if ($autoPart->category_name) {
                $parentCategory = \App\Models\Category::firstOrCreate(
                    ['name' => $autoPart->category_name],
                    [
                        'slug' => SlugService::createSlug(\App\Models\Category::class, 'slug', $autoPart->category_name),
                        'is_active' => true,
                    ]
                );

                if (!empty($autoPart->sub_category_name)) {
                    $subCategory = \App\Models\Category::firstOrCreate(
                        [
                            'name' => $autoPart->sub_category_name,
                            'parent_id' => $parentCategory->id,
                        ],
                        [
                            'slug' => SlugService::createSlug(\App\Models\Category::class, 'slug', $autoPart->sub_category_name),
                            'is_active' => true,
                        ]
                    );

                    $autoPart->category_id = $subCategory->id;
                } else {
                    $autoPart->category_id = $parentCategory->id;
                }
            }


        });

        // static::created(function ($autoPart) {
        //     $compatibilities = request('data.compatibleVehicles') ?? [];

        //     foreach ($compatibilities as $entry) {
        //         if (!empty($entry['car_model_id']) && !empty($entry['year'])) {
        //             $autoPart->compatibleVehicles()->attach($entry['car_model_id'], [
        //                 'year_from' => $entry['year'],
        //                 'year_to' => $entry['year'],
        //                 'is_verified' => true,
        //             ]);
        //         }
        //     }
        // });

        // static::updated(function ($autoPart) {
        //     $autoPart->compatibleVehicles()->detach();

        //     $compatibilities = request('data.compatibleVehicles') ?? [];

        //     foreach ($compatibilities as $entry) {
        //         if (!empty($entry['car_model_id']) && !empty($entry['year'])) {
        //             $autoPart->compatibleVehicles()->attach($entry['car_model_id'], [
        //                 'year_from' => $entry['year'],
        //                 'year_to' => $entry['year'],
        //                 'is_verified' => true,
        //             ]);
        //         }
        //     }
        // });
    }

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
     * Media Collections
     */
    public function registerMediaCollections(): void
    {
        $imageMimeTypes = [
            'image/jpeg', 'image/png', 'image/webp',
            'image/gif', 'image/bmp', 'image/svg+xml', 'image/tiff',
        ];

        $this->addMediaCollection('auto_part_feature_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('auto_part_secondary_image')
            ->singleFile()
            ->acceptsMimeTypes($imageMimeTypes);

        $this->addMediaCollection('auto_part_gallery')
            ->acceptsMimeTypes(array_merge($imageMimeTypes, [
                'video/mp4',
                'video/mpeg',
                'video/quicktime',
            ]));
    }

    // Relationships
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withPivot('is_primary')
            ->withTimestamps();
    }


    public function viscosityGrade()
    {
        return $this->belongsTo(\App\Models\ViscosityGrade::class);
    }

    public function autoPartBrand()
    {
        return $this->belongsTo(\App\Models\AutoPartBrand::class);
    }

    public function autoPartCountry()
    {
        return $this->belongsTo(\App\Models\AutoPartCountry::class);
    }

    
    // public function compatibleVehicles()
    // {
    //     return $this->belongsToMany(\App\Models\CarModel::class, 'product_car_compatibility', 'product_id', 'car_model_id')
    //         ->withPivot(['year_from', 'year_to', 'notes', 'is_verified'])
    //         ->withTimestamps();
    // }
    /**
     * Accessors
     */
    public function getAutoPartFeatureImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('auto_part_feature_image') ?: null;
    }

    public function getAutoPartSecondaryImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('auto_part_secondary_image') ?: null;
    }

    public function getAutoPartGalleryUrls(string $conversion = null): array
    {
        return $this->getMedia('auto_part_gallery')
            ->map(fn($media) => $conversion && $media->hasGeneratedConversion($conversion)
                ? $media->getUrl($conversion)
                : $media->getUrl())
            ->toArray();
    }

    public function reviews()
    {
        return $this->morphMany(ProductReview::class, 'product');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

    public function setSlugAttribute($value)
    {
        $clean = preg_replace('/\s+/', ' ', trim($value)); 
        $this->attributes['slug'] = Str::slug($clean);
    }

}
