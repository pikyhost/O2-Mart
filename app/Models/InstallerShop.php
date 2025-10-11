<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class InstallerShop extends Model
{
    protected $fillable = ['name', 'location'];

    protected $table = 'installer_shops';


    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class);
    }

     public function getWorkingHoursDisplayAttribute(): array
    {
        $hours = $this->relationLoaded('workingHours')
            ? $this->workingHours->load('day')
            : $this->workingHours()->with('day')->get();

        $workingDays = [];
        $closedDays = [];

        foreach ($hours as $wh) {
            if (!$wh->day) continue;
            
            $dayName = substr($wh->day->name, 0, 3); // Get first 3 chars (Mon, Tue, etc.)
            $isClosed = (bool)$wh->is_closed;
            
            if ($isClosed || !$wh->opening_time || !$wh->closing_time) {
                $closedDays[] = $dayName;
            } else {
                $open = Carbon::createFromFormat('H:i:s', $wh->opening_time)->format('g:i A');
                $close = Carbon::createFromFormat('H:i:s', $wh->closing_time)->format('g:i A');
                $workingDays[] = [
                    'abbr' => $dayName,
                    'hours' => "{$open} – {$close}"
                ];
            }
        }

        $out = [];
        
        // Group working days by hours
        $hourGroups = [];
        foreach ($workingDays as $day) {
            $hourGroups[$day['hours']][] = $day['abbr'];
        }
        
        foreach ($hourGroups as $hours => $days) {
            $daysList = implode(', ', $days);
            $out[] = "{$daysList}: {$hours}";
        }
        
        if (!empty($closedDays)) {
            $days = implode(', ', $closedDays);
            $out[] = "{$days}: Closed";
        }

        return $out;
    }

}
