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

        $seq = [];
        foreach ($daysMeta as $id => $meta) {
            $wh = $byDay->get($id);
            // If no working hour record exists, treat as closed
            $isClosed = !$wh || (bool) $wh->is_closed;
            $open  = ($wh && !$isClosed) ? $wh->opening_time : null;
            $close = ($wh && !$isClosed) ? $wh->closing_time : null;
            $key   = $isClosed ? 'closed' : "{$open}-{$close}"; 

            $seq[] = [
                'abbr'      => $meta['abbr'],
                'is_closed' => $isClosed,
                'open'      => $open,
                'close'     => $close,
                'key'       => $key,
            ];
        }

        $groups = [];
        $current = null;

        foreach ($seq as $row) {
            if ($current === null || $current['key'] !== $row['key']) {
                $current = ['from' => $row, 'to' => $row, 'key' => $row['key']];
                $groups[] = $current; 
            } else {
                $current['to'] = $row;
                $groups[count($groups) - 1] = $current;
            }
        }

        $out = [];
        foreach ($groups as $g) {
            $label = $g['from']['abbr'] === $g['to']['abbr']
                ? $g['from']['abbr']
                : "{$g['from']['abbr']}–{$g['to']['abbr']}";

            if ($g['key'] === 'closed') {
                $out[] = "{$label}: Closed";
            } else {
                $open  = Carbon::createFromFormat('H:i:s', $g['from']['open'])->format('g:i A');
                $close = Carbon::createFromFormat('H:i:s', $g['from']['close'])->format('g:i A');
                $out[] = "{$label}: {$open} – {$close}";
            }
        }

        return $out;
    }

}
