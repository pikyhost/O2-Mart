<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RimCountry extends Model
{
    protected $fillable = ['name'];

    public function rims()
    {
        return $this->hasMany(Rim::class, 'rim_country_id');
    }
}
