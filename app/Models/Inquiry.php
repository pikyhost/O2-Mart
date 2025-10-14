<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Inquiry extends Model
{
    protected $fillable = [
        'type', 'status', 'priority', 'full_name', 'phone_number', 'email',
        'car_make', 'car_model', 'car_year', 'vin_chassis_number',
        'required_parts', 'quantity', 'battery_specs', 'description',
        'car_license_photos', 'part_photos', 'admin_notes', 'quoted_price',
        'quoted_at', 'assigned_to', 'source', 'page_source', 'ip_address', 'user_agent',
        'rim_size', 'front_width', 'front_height', 'front_diameter',
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

    public function getCarLicensePhotosAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getPartPhotosAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setCarLicensePhotosAttribute($value)
    {
        $this->attributes['car_license_photos'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setPartPhotosAttribute($value)
    {
        $this->attributes['part_photos'] = is_array($value) ? json_encode($value) : $value;
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

    // Accessors & Mutators
    protected function carLicensePhotosUrls(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->car_license_photos
                ? collect($this->car_license_photos)->map(fn ($path) => Storage::url($path))->toArray()
                : []
        );
    }

    protected function partPhotosUrls(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->part_photos
                ? collect($this->part_photos)->map(fn ($path) => Storage::url($path))->toArray()
                : []
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
        return !empty($this->car_license_photos) || !empty($this->part_photos);
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
