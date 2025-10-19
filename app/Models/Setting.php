<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'country_id',
        'currency_id',
        'logo',
        'dark_logo',
        'favicon',
        'phone',
        'email',
        'facebook',
        'youtube',
        'instagram',
        'x',
        'snapchat',
        'tiktok',
    ];

    protected static string $cacheKey = 'app_settings';

    /**
     * Boot method to clear cache on updates.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(fn () => self::reloadCache());
        static::deleted(fn () => self::reloadCache());
    }

    /**
     * Get all settings from cache or database.
     */
    public static function getAllSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Cache::rememberForever(self::$cacheKey, function () {
            return self::query()->first()?->toArray() ?? [];
        });
    }

    /**
     * Retrieve a specific setting value.
     */
    public static function getSetting(string $key): mixed
    {
        $settings = self::getAllSettings();

        return $settings[$key] ?? null;
    }

    /**
     * Update settings and refresh the cache.
     */
    public static function updateSettings(array $data): bool
    {
        try {
            $settings = self::firstOrNew();
            $settings->fill($data);
            $settings->save();

            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to update settings: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Reload settings cache.
     */
    public static function reloadCache(): void
    {
        Cache::forget(self::$cacheKey);

        $settings = self::query()->first()?->toArray() ?? [];
        Cache::forever(self::$cacheKey, $settings);
    }

    /**
     * Clear the settings cache manually.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::$cacheKey);
    }

    /**
     * Currency relationship.
     */
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Country relationship.
     */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the currency with its symbol (with caching).
     */
    public static function getCurrency(): ?Currency
    {
        $settings = self::getAllSettings();

        if (empty($settings['currency_id'])) {
            return null;
        }

        return Cache::rememberForever("currency_{$settings['currency_id']}", function () use ($settings) {
            return Currency::find($settings['currency_id']);
        });
    }

    /**
     * Get contact details (phone and email).
     */
    public static function getContactDetails(): array
    {
        return [
            'phone' => self::getSetting('phone'),
            'email' => self::getSetting('email'),
        ];
    }

    /**
     * Get social media links.
     */
    public static function getSocialMediaLinks(): array
    {
        return [
            'facebook'  => self::getSetting('facebook'),
            'youtube'   => self::getSetting('youtube'),
            'instagram' => self::getSetting('instagram'),
            'linkedin'  => self::getSetting('x'), // Use x column for LinkedIn
            'snapchat'  => self::getSetting('snapchat'),
            'tiktok'    => self::getSetting('tiktok'),
        ];
    }
}
