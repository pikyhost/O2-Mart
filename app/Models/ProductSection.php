<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSection extends Model
{
    protected $fillable = [
        'type',
        'background_image',
        'section1_title',
        'section1_text1',
        'section1_text2',
        'section2_title',
        'section2_text',
        'meta_title',
        'meta_description',
        'alt_text',
    ];
}
