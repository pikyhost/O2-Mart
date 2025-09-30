<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Sluggable;

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

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'category_attributes')
            ->withPivot('is_required', 'sort_order')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function rims()
    {
        return $this->hasMany(Rim::class, 'category_id');
    }
}
