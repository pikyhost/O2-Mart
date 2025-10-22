<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_needed' => 'boolean',
        'delay_seconds' => 'integer',
        'specific_pages' => 'array', // Cast JSON string to array
    ];

    /**
     * Scope to get only active popups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
