<?php

namespace App\Services;

use App\Models\CompareList;
use Illuminate\Support\Facades\Auth;

class CompareService
{
    public static function mergeSessionCompareWithUserCompare(?string $sessionId = null): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $userList = CompareList::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'session_id' => null,
            'product_type' => null,
        ]);

        if (!$sessionId) {
            return;
        }

        $guestList = CompareList::where('session_id', $sessionId)->first();
        if (!$guestList) {
            return;
        }

        foreach ($guestList->items as $item) {
            $userList->items()->firstOrCreate([
                'buyable_type' => $item->buyable_type,
                'buyable_id'   => $item->buyable_id,
            ]);
        }

        if (!$userList->product_type && $guestList->product_type) {
            $userList->update(['product_type' => $guestList->product_type]);
        }

        $guestList->delete();
    }
}
