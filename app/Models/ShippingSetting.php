<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $table = 'shipping_settings';

    protected $fillable = [
        'tiers',
        'extra_per_kg',
        'fuel_percent',
        'epg_percent',
        'epg_min',
        'packaging_fee',
        'vat_percent',
        'volumetric_divisor',
        'remote_areas',
        'installation_fee', 
        
    ];

    protected $casts = [
        'tiers' => 'array',
        'extra_per_kg' => 'float',
        'fuel_percent' => 'float',
        'epg_percent' => 'float',
        'epg_min' => 'float',
        'packaging_fee' => 'float',
        'vat_percent' => 'float',
        'volumetric_divisor' => 'float',
        'remote_areas' => 'array',
        'installation_fee' => 'float',
    ];

    public $timestamps = false;
}
