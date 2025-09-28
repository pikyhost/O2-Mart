<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'buyable_type',
        'buyable_id',
        'quantity',
        'price_per_unit',
        'subtotal',
        'product_name',
        'sku',
        'image_url',
        'shipping_option',
        'installation_center_id',
        'mobile_van_id',
        'installation_date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyable()
    {
        return $this->morphTo();
    }

    public function installationCenter()
    {
        return $this->belongsTo(InstallerShop::class);
    }

    public function mobileVan()
    {
        return $this->belongsTo(MobileVanService::class, 'mobile_van_id');
    }

}
