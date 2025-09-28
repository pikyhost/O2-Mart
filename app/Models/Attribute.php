<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'options',
        'unit',
        'is_required',
        'is_filterable',
        'is_searchable',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attributes')
            ->withPivot('is_required', 'sort_order')
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function carModels(): BelongsToMany
    {
        return $this->belongsToMany(CarModel::class, 'car_model_attributes')
            ->withPivot('value')
            ->withTimestamps();
    }
}
