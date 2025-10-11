<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class MobileVanService extends Model
{
    protected $fillable = ['name', 'location'];

    protected $table = 'mobile_van_services';


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

    protected $appends = ['working_hours_display'];

   public function getWorkingHoursDisplayAttribute(): array
    {
        $daysMeta = [
            1 => ['abbr' => 'Mon'],
            2 => ['abbr' => 'Tue'],
            3 => ['abbr' => 'Wed'],
            4 => ['abbr' => 'Thu'],
            5 => ['abbr' => 'Fri'],
            6 => ['abbr' => 'Sat'],
            7 => ['abbr' => 'Sun'],
        ];

        $hours = $this->relationLoaded('workingHours')
            ? $this->workingHours
            : $this->workingHours()->get();

        $byDay = $hours->keyBy('day_id');
        $workingDays = [];
        $closedDays = [];

        foreach ($daysMeta as $id => $meta) {
            $wh = $byDay->get($id);
            $isClosed = !$wh || $wh->is_closed == 1 || $wh->is_closed === true || $wh->is_closed === '1';
            
            if ($isClosed) {
                $closedDays[] = $meta['abbr'];
            } else {
                $open = Carbon::createFromFormat('H:i:s', $wh->opening_time)->format('g:i A');
                $close = Carbon::createFromFormat('H:i:s', $wh->closing_time)->format('g:i A');
                $workingDays[] = [
                    'abbr' => $meta['abbr'],
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
