<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TyreAttribute extends Model
{
    protected $fillable = ['car_make_id', 'car_model_id', 'model_year', 'trim', 'tyre_attribute', 'rare_attribute',  'tyre_oem'];
    protected $casts = [
        'tyre_oem' => 'boolean',
    ];

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function tyres()
    {
        return $this->hasMany(Tyre::class);
    }


}
