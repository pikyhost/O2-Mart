<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatteryDimension extends Model
{
    use HasFactory;

    protected $fillable = ['value'];

    public function batteries()
    {
        return $this->hasMany(Battery::class, 'dimension_id');
    }
}
