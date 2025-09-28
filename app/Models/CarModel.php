<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarModel extends Model
{
    protected $fillable = [
        'car_make_id',
        'name',
        'slug',
        'year_from',
        'year_to',
        // 'generation',
        // 'fuel_type',
        // 'engine_size',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // app/Models/CarModel.php

    public function compatibleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_car_compatibility')
            ->withPivot(['year_from', 'year_to', 'notes', 'is_verified'])
            ->withTimestamps();
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'car_model_attributes')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function carModelAttributes()
    {
        return $this->hasMany(CarModelAttribute::class);
    }
    public function getYearsAttribute()
    {
        return range($this->year_from, $this->year_to);
    }
}
