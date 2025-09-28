<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContactUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'heading',
        'description',
        'title',
        'title_desc',
        'form_title',
        'form_desc',
        'address',
        'email',
        'whatsapp',
        'meta_title',
        'meta_description',
        'alt_text', 
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : null;
    }

}
