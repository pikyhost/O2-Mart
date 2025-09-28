<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // Add this scope method to your UserAddress model
    public function scopeActive($query, $active = true)
    {
        return $query->where('is_active', $active);
    }
}
