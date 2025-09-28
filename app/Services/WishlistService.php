<?php

namespace App\Services;

use App\Models\Wishlist;

class WishlistService
{
    public static function getCurrentWishlist(): Wishlist
    {
        $user = auth('sanctum')->user();
        $sessionId = request()->header('X-Session-ID') ?? session()->getId();

        if ($user) {
            return Wishlist::firstOrCreate(['user_id' => $user->id]);
        } elseif ($sessionId) {
            return Wishlist::firstOrCreate(['session_id' => $sessionId]);
        }

        throw new \Exception('Unauthenticated or session ID missing.');
    }
}
