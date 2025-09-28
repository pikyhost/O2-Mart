<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    // Add this scope method to your UserAddress model
    public function scopeActive($query, $active = true)
    {
        return $query->where('is_active', $active);
    }
}
