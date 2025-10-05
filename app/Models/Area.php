<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'shipping_cost',
        'shipping_estimate_time',
        'is_active',
        'is_remote'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_remote' => 'boolean',
        'shipping_cost' => 'decimal:2',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Scopes
    public function scopeActive($query, $active = true)
    {
        return $query->where('is_active', $active);
    }

    // Accessors
    public function getFormattedShippingAttribute()
    {
        return "{$this->shipping_cost} ({$this->shipping_estimate_time} days)";
    }
}
