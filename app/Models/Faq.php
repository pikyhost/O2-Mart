<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['items', 'background_image'];


    protected $casts = [
        'items' => 'array',
    ];
}
