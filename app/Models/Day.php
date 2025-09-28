<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $fillable = ['name', 'created_at'];

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }
}
