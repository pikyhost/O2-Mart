<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RimSize extends Model
{
    protected $fillable = ['size'];

    public function rims()
    {
        return $this->hasMany(Rim::class, 'rim_size_id');
    }
}
