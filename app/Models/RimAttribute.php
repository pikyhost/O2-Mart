<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RimAttribute extends Model
{
    protected $fillable = ['car_make_id', 'car_model_id', 'model_year', 'name'];

    public function make()
    {
        return $this->belongsTo(\App\Models\CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(\App\Models\CarModel::class, 'car_model_id');
    }
}

