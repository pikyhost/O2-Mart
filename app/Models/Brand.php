<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
    ];
    protected $appends = ['logo_url'];



    /**
     * Register media collections and conversions
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }



    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo'); 
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
