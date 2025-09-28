<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryAttribute extends Model
{
    protected $fillable = ['car_make_id', 'car_model_id', 'model_year', 'name'];

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}

