<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryCapacity extends Model
{
    protected $fillable = ['value'];

    public function batteries()
    {
        return $this->hasMany(Battery::class, 'capacity_id');
    }
}
