<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoPartCountry extends Model
{
    protected $fillable = ['name'];

    public function autoParts()
    {
        return $this->hasMany(AutoPart::class, 'auto_part_country_id');
    }
}

