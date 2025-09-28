<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTyreSpec extends Model
{
    protected $table = 'car_tyre_specs';
    protected $primaryKey = 'id'; // this is default, but add it explicitly if needed
    public $incrementing = true;

    protected $fillable = [
        'car_make',
        'car_model',
        'car_year',
        'engine_performance',
        'tyre_size',
        'tyre_oem',
    ];
}
