<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Inquiry extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'type', 'status', 'priority', 'full_name', 'phone_number', 'email',
        'car_make', 'car_model', 'car_year', 'vin_chassis_number',
        'required_parts', 'quantity', 'battery_specs', 'description',
        'admin_notes', 'quoted_price', 'quoted_at', 'assigned_to', 'source', 'page_source',
        'ip_address', 'user_agent', 'rim_size', 'rim_size_id', 'front_width', 'front_height', 'front_diameter',
        'rear_tyres'
    ];

    protected $casts = [
        'required_parts' => 'array',
        'quoted_at' => 'datetime',
        'quoted_price' => 'decimal:2',
        'quantity' => 'integer',
        'car_year' => 'integer',
        'rear_tyres' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_license_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']);
            
        $this->addMediaCollection('part_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']);
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(350)
            ->height(350)
            ->sharpen(10)
            ->performOnCollections('car_license_photos', 'part_photos');
            
        $this->addMediaConversion('preview')
            ->width(800)
            ->height(800)
            ->performOnCollections('car_license_photos', 'part_photos');
    }

    protected $hidden = ['ip_address', 'user_agent'];

    // Constants
    public const TYPES = [
        'rims' => 'Rims',
        'auto_parts' => 'Auto Parts',
        'battery' => 'Battery',
        'tires' => 'Tires'
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'quoted' => 'Quoted',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    // Relationships
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function rimSize(): BelongsTo
    {
        return $this->belongsTo(\App\Models\RimSize::class, 'rim_size_id');
    }

    // Accessors & Mutators
    protected function carLicensePhotosUrls(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMedia('car_license_photos')->map(fn ($media) => $media->getUrl())->toArray()
        );
    }

    protected function partPhotosUrls(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMedia('part_photos')->map(fn ($media) => $media->getUrl())->toArray()
        );
    }

    protected function carMake(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->attributes['car_make'] ?? null;
                // If value is numeric (ID), look up the name
                if ($value && is_numeric($value)) {
                    $carMake = \App\Models\CarMake::find($value);
                    return $carMake?->name ?? $value;
                }
                return $value;
            }
        );
    }
    
    protected function carModel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->attributes['car_model'] ?? null;
                // If value is numeric (ID), look up the name
                if ($value && is_numeric($value)) {
                    $carModel = \App\Models\CarModel::find($value);
                    return $carModel?->name ?? $value;
                }
                return $value;
            }
        );
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::TYPES[$this->type] ?? $this->type
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->status] ?? $this->status
        );
    }

    // Query Scopes
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeWithFiles(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNotNull('car_license_photos')
                ->orWhereNotNull('part_photos');
        });
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    // Helper Methods
    public function isQuoted(): bool
    {
        return $this->status === 'quoted' && $this->quoted_price > 0;
    }

    public function hasFiles(): bool
    {
        return $this->getMedia('car_license_photos')->isNotEmpty() || $this->getMedia('part_photos')->isNotEmpty();
    }



    public function getRequiredPartsString(): string
    {
        return is_array($this->required_parts)
            ? implode(', ', $this->required_parts)
            : (string) $this->required_parts;
    }

    // Boot method for observers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            $inquiry->ip_address = request()->ip();
            $inquiry->user_agent = request()->userAgent();
        });
    }
}
