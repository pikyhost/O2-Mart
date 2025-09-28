<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompareList extends Model
{
    protected $fillable = ['user_id', 'session_id', 'product_type'];

    public function items()
    {
        return $this->hasMany(CompareItem::class);
    }
}
