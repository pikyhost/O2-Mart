<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'subtotal', 'total',
        'tax_percentage', 'tax_amount', 'shipping_cost',
        'country_id', 'governorate_id', 'city_id', 'area_id','applied_coupon', 
        'discount_amount', 
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}

