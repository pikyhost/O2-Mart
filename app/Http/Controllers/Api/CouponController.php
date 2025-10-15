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
            return response()->json(['message' => 'Your cart is empty. Please add items before applying a coupon.'], 400);
        }

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'This coupon code is invalid or has expired. Please check the code and try again.'], 400);
        }

        // Get current cart total from existing cart calculation
        $currentTotal = $cart->total ?? 0;
        
        // If no current total, calculate it simply
        if ($currentTotal == 0) {
            foreach ($cart->items as $item) {
                if (!$item->buyable) continue;
                $currentTotal += $item->subtotal;
            }
        }

        if ($coupon->min_order_amount && $currentTotal < $coupon->min_order_amount) {
            return response()->json(['message' => 'This coupon requires a minimum order of ' . number_format($coupon->min_order_amount, 2) . ' AED. Your current total is ' . number_format($currentTotal, 2) . ' AED.'], 400);
        }

        // Count total usage from both CouponUsage table and completed orders
        $totalUsedFromCoupons = CouponUsage::where('coupon_id', $coupon->id)->count();
        $totalUsedFromOrders = \App\Models\Order::where('coupon_id', $coupon->id)
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->count();
        $totalUsed = $totalUsedFromCoupons + $totalUsedFromOrders;
        
        // Use same logic as cart system for user/session identification
        $user = auth('sanctum')->user();
        $sessionId = request()->header('X-Session-ID') ?? session()->getId();
        
        // Count usage by current user/session only from completed orders (not cart applications)
        $usedByCurrentFromOrders = \App\Models\Order::where('coupon_id', $coupon->id)
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user && $sessionId, fn($q) => $q->where('checkout_token', $sessionId))
            ->count();
        
        // Count total usage only from completed orders
        $totalUsedFromOrders = \App\Models\Order::where('coupon_id', $coupon->id)
            ->whereIn('status', ['completed', 'delivered', 'paid'])
            ->count();

        if ($coupon->usage_limit && $totalUsedFromOrders >= $coupon->usage_limit) {
            return response()->json(['message' => 'This coupon has reached its usage limit and is no longer available.'], 400);
        }

        if ($coupon->usage_limit_per_user && $usedByCurrentFromOrders >= $coupon->usage_limit_per_user) {
            return response()->json(['message' => 'You have already used this coupon the maximum number of times allowed.'], 400);
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
            return response()->json(['message' => 'Your cart could not be found. Please refresh the page and try again.'], 404);
        }

        if (!$cart->applied_coupon) {
            return response()->json(['message' => 'No coupon is currently applied to your cart.'], 400);
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
