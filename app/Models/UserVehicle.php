<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVehicle extends Model
{
    protected $fillable = [
        'user_id',
        'car_make_id',
        'car_model_id',
        'car_year',
        'year',
        'trim',
        'engine_size',
        'vin',
        'mileage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

}
