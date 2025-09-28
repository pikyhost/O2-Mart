<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TyreCountry extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function tyres()
    {
        return $this->hasMany(Tyre::class);
    }
}
