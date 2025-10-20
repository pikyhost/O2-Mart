<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_id',
        'coupon_id',
        'shipping_cost',
        'tax_percentage',
        'tax_amount',
        'subtotal',
        'total',
        'discount',
        'installation_fees',
        'country_id',
        'governorate_id',
        'city_id',
        'status',
        'tracking_number',
        'tracking_url',
        'shipping_response',
        'payment_method',
        'paymob_order_id', 
        'notes',
        'checkout_token',
        'shipping_status',
        'shipping_response',
        'shipping_breakdown',
        'tracking_number',
        'shipping_company',
        'car_make',
        'car_model',
        'car_year',
        'plate_number',
        'vin',
        'title',

    ];

    protected $casts = [
        'shipping_breakdown' => 'array',
        'shipping_response'  => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }


}
