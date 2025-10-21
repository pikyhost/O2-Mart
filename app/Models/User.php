<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\LogsModelActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, HasPermissions,HasApiTokens, LogsModelActivity;

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }
    
    protected static $logName = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['first_name', 'last_name', 'display_name'];

    public function canAccessPanel(Panel $panel): bool
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return $this->hasAnyRole([
                UserRole::SuperAdmin->value,
                UserRole::Admin->value,
            ]);
        }
        return false;
    }

    public function likedBlogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_user_likes')->withTimestamps();
    }
    public function vehicles()
    {
        return $this->hasMany(UserVehicle::class);
    }
    
    public function userVehicles()
    {
        return $this->hasMany(UserVehicle::class);
    }

    public function userLoginToken(): HasOne
    {
        return $this->hasOne(UserLoginToken::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function primaryAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_primary', true);
    }
    public function cart(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    // First name
    public function getFirstNameAttribute()
    {
        $parts = explode(' ', $this->name ?? '');
        return trim($parts[0] ?? '');
    }
    public function setFirstNameAttribute($value)
    {
        $last = $this->last_name; 
        $this->attributes['name'] = trim("{$value} {$last}");
    }

    // Last name
    public function getLastNameAttribute()
    {
        $parts = explode(' ', $this->name ?? '');
        array_shift($parts);
        return trim(implode(' ', $parts));
    }

    public function setLastNameAttribute($value)
    {
        $first = $this->first_name; 
        $this->attributes['name'] = trim("{$first} {$value}");
    }

    // Display name
    public function getDisplayNameAttribute()
    {
        return $this->desc_for_comment ?? '';
    }

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['desc_for_comment'] = $value;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
