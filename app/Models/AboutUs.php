<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AboutUs extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'about_us';

    protected $fillable = [
        'intro_title',
        'intro_text',
        'intro_cta',
        'intro_url',
        'center_title',
        'center_text',
        'center_cta',
        'center_url',
        'latest_title',
        'latest_text',
        'about_us_video_path',
        'center_image_path',
        'latest_image_path',
        'slider_1_image',
        'slider_2_image',
        'intro_image',
        'first_section_title',
        'first_section_desc',
        'meta_title',
        'meta_description',
        'alt_text',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('about_us_video')
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/quicktime'])
            ->singleFile();
    }
}
