<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'country_id',
        'governorate_id',
        'city_id',
        'area_text',
        'area_id',
        'address_line',
        'phone',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function governorate()
    {
        return $this->belongsTo(\App\Models\Governorate::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}
