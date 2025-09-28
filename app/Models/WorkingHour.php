<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingHour extends Model
{
    protected $fillable = [
        'center_id',
        'gas_station_id',
        'day_id',
        'opening_time',
        'closing_time',
        'is_closed'
    ];

    // Relationships
    public function mobileVanService(): BelongsTo
    {
        return $this->belongsTo(MobileVanService::class);
    }

    public function installerShop(): BelongsTo
    {
        return $this->belongsTo(InstallerShop::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    protected static function booted(): void
    {
        static::saving(function (self $workingHour) {
            if ($workingHour->is_closed) {
                $workingHour->opening_time = null;
                $workingHour->closing_time = null;
            }
        });
    }
}
