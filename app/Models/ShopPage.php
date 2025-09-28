<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPage extends Model
{
    protected $fillable = [
        'section_1_title', 'section_1_content',
        'section_2_title', 'section_2_content', 'section_2_image',
        'meta_title', 'meta_description', 'alt_text', 

    ];
}