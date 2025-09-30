<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TyreSize extends Model
{
    use HasFactory;

    protected $fillable = ['size'];

    public function tyres()
    {
        return $this->hasMany(Tyre::class, 'tyre_size_id');
    }
}
