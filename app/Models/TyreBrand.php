<?php

// app/Models/TyreBrand.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TyreBrand extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['name'];

    public function tyres()
    {
        return $this->hasMany(Tyre::class, 'tyre_brand_id');
    }
        public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}
