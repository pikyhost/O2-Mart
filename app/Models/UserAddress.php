<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'user_id',
        'session_id',
        'label',
        'full_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'country_id',
        'governorate_id',
        'city_id',
        'area_id',
        'postal_code',
        'additional_info',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];
    protected static $logName = 'user_address';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Ensure only one primary address per user/session
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($address) {
            if ($address->is_primary) {
                $query = static::query();

                if ($address->user_id) {
                    $query->where('user_id', $address->user_id);
                } else {
                    $query->where('session_id', $address->session_id);
                }

                $query->where('id', '!=', $address->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    public function scopeForUserOrSession($query, $userId = null, $sessionId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Append full location details
    protected $appends = ['full_location'];

    public function getFullLocationAttribute()
    {
        $location = [];

        if ($this->area) {
            $location[] = $this->area->name;
        }
        if ($this->city) {
            $location[] = $this->city->name;
        }
        if ($this->governorate) {
            $location[] = $this->governorate->name;
        }
        if ($this->country) {
            $location[] = $this->country->name;
        }

        return implode(', ', $location);
    }
}
