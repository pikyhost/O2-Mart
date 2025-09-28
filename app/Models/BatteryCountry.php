<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryCountry extends Model
{
    use HasFactory;

    // protected $fillable = ['name', 'code'];

     protected $guarded = [];
    
    public function batteries()
    {
        return $this->hasMany(Battery::class, 'battery_country_id');
    }
}
