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
        // Mon → Sun
        $daysMeta = [
            2 => ['abbr' => 'Mon'],
            3 => ['abbr' => 'Tue'],
            4 => ['abbr' => 'Wed'],
            5 => ['abbr' => 'Thu'],
            6 => ['abbr' => 'Fri'],
            7 => ['abbr' => 'Sat'],
            1 => ['abbr' => 'Sun'],
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
