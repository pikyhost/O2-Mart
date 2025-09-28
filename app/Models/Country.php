<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];

    public function scopeActive($query, $active = true)
    {
        return $query->where('is_active', $active);
    }
}
