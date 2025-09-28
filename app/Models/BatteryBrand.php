<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class BatteryBrand extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['value'];

    public function batteries()
    {
        return $this->hasMany(Battery::class, 'battery_brand_id');
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
