<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'buyable_type', 'buyable_id',
        'quantity', 'price_per_unit', 'subtotal',
        'shipping_option', 'mobile_van_id',
        'installation_center_id', 'installation_date',
    ];


    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function buyable(): MorphTo
    {
        return $this->morphTo();
    }

    public function mobileVan()
    {
        return $this->belongsTo(MobileVanService::class, 'mobile_van_id');
    }

    public function installationCenter()
    {
        return $this->belongsTo(InstallerShop::class, 'installation_center_id');
    }
}

