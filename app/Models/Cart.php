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

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}

