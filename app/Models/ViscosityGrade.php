<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViscosityGrade extends Model
{
    protected $fillable = ['name'];

    public function autoParts()
    {
        return $this->hasMany(AutoPart::class, 'viscosity_grade_id');
    }
}

