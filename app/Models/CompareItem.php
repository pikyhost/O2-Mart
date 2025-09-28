<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CompareItem extends Model
{
    protected $fillable = ['compare_list_id', 'buyable_type', 'buyable_id']; 

    public function compareList()
    {
        return $this->belongsTo(CompareList::class);
    }

    public function buyable(): MorphTo
    {
        return $this->morphTo();
    }



}
