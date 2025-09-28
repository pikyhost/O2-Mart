<?php

// app/Models/ProductAttributePivot.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductAttributePivot extends Pivot
{
    protected $casts = [
        'boolean_value' => 'boolean',
        'json_value' => 'array',
        'number_value' => 'float',
    ];
}
