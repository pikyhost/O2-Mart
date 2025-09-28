<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VinRecord extends Model
{
    protected $fillable = ['vin', 'car_model_id'];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
