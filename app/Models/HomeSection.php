<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $table = 'home_sections';

    protected $casts = [
        'categories_boxes' => 'array',
    ];
    protected $fillable = [
        'section_1_title', 'section_1_text', 'section_1_image',
        'section_1_cta_text', 'section_1_cta_link',
        'section_2_title', 'section_2_text', 'section_2_image',
        'section_3_title', 'section_3_text', 'section_3_image',
        'section_4_image', 'section_4_link',
        'banner_1_image', 'banner_1_link',
        'banner_2_image', 'banner_2_link',
        'blog_section_title', 'blog_section_text', 
        'categories_boxes', 'meta_title', 'meta_description', 'alt_text',
        'tagline',
    ];



}
