<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'summary', 'review', 'rating', 'product_id', 'product_type', 'is_approved'
    ];

    public function product()
    {
        return $this->morphTo();
    }
}

