<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use Sluggable, LogsModelActivity;

    /**
     * Configure the settings for generating slugs.
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title'],
        ];
    }
    protected static $logName = 'product';

    /**
     * Get the route key name for Laravel routing.
     */
    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }


    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'manage_stock' => 'boolean',
        'dimensions' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'cross_reference_numbers' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function compatibleCarModels(): BelongsToMany
    {
        return $this->belongsToMany(CarModel::class, 'product_car_compatibility')
            ->withPivot(['year_from', 'year_to', 'notes', 'is_verified'])
            ->withTimestamps();
    }

    public function primaryCategory()
    {
        return $this->categories()->wherePivot('is_primary', true)->first();
    }

    public function compatibilityRules()
    {
        return $this->hasMany(\App\Models\CompatibilityRule::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
