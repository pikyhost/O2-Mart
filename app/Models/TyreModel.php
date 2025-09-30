<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TyreModel extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function tyres()
    {
        return $this->hasMany(Tyre::class, 'tyre_model_id');
    }
}
