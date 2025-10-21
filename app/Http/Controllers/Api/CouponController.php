<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Services\CartService;
use App\Services\ShippingCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $cart = CartService::getCurrentCart()->load('items.buyable');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Please add items to your cart before applying a coupon.'], 400);
        }

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Coupon code not found or has expired.'], 400);
        }

        // Get cart summary for proper total calculation
        $cartSummary = \App\Services\CartService::generateCartSummary($cart);
        $currentTotal = $cartSummary['totals']['items_subtotal'];

        // Check min order amount
        if ($coupon->min_order_amount && $currentTotal < $coupon->min_order_amount) {
            return response()->json(['message' => 'Minimum order of ' . number_format($coupon->min_order_amount, 2) . ' AED required for this coupon.'], 400);
        }

        // User/session identification
        $user = auth('sanctum')->user();
        $sessionId = request()->header('X-Session-ID') ?? session()->getId();
        
        // Check total usage limit (only check CouponUsage since it's created when orders are placed)
        if ($coupon->usage_limit) {
            $totalUsed = CouponUsage::where('coupon_id', $coupon->id)
                ->whereNotNull('order_id') // Only count actual order usages
                ->count();
            
            if ($totalUsed >= $coupon->usage_limit) {
                return response()->json(['message' => 'This coupon is no longer available.'], 400);
            }
        }

        // Check per user/session usage limit (only check CouponUsage since it's created when orders are placed)
        if ($coupon->usage_limit_per_user) {
            $userUsed = CouponUsage::where('coupon_id', $coupon->id)
                ->whereNotNull('order_id') // Only count actual order usages
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->when(!$user && $sessionId, fn($q) => $q->where('session_id', $sessionId))
                ->count();
            
            if ($userUsed >= $coupon->usage_limit_per_user) {
                return response()->json(['message' => 'You have already used this coupon.'], 400);
            }
        }

        // Calculate discount
        $discount = 0;
        switch ($coupon->type) {
            case 'discount_amount':
                $discount = min($coupon->value, $currentTotal);
                break;
            case 'discount_percentage':
                $discount = round($currentTotal * ($coupon->value / 100), 2);
                break;
            case 'free_shipping':
                $discount = $cart->shipping_cost ?? 0;
                break;
        }

        // Apply discount directly to total
        $newTotal = max(0, $currentTotal - $discount);

        $cart->update([
            'applied_coupon' => $coupon->code,
            'discount_amount' => $discount,
            'total' => $newTotal,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully',
            'coupon_code' => $coupon->code,
            'discount_applied' => $discount,
            'new_total' => $newTotal,
        ]);
    }

    public function remove(Request $request)
    {
        $cart = CartService::getCurrentCart()->load('items.buyable');

        if (!$cart) {
            return response()->json(['message' => 'Cart not found. Please refresh and try again.'], 404);
        }

        if (!$cart->applied_coupon) {
            return response()->json(['message' => 'No coupon to remove.'], 400);
        }

        // Restore original total by adding back the discount
        $originalTotal = $cart->total + ($cart->discount_amount ?? 0);

        $cart->update([
            'applied_coupon' => null,
            'discount_amount' => 0,
            'total' => $originalTotal,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon removed successfully',
            'new_total' => $originalTotal,
        ]);
    }

}
