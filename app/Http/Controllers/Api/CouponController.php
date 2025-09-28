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
            'area_id' => 'nullable|integer',
        ]);

        $cart = CartService::getCurrentCart();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        if ($request->filled('area_id')) {
            $cart->update(['area_id' => $request->area_id]);
        }

        $shipping = [];
        if ($cart->area_id) {
            $shipping = ShippingCalculatorService::calculate($cart, 5);
            if (!empty($shipping['error'])) {
                return response()->json(['message' => 'Shipping calculation failed.'], 400);
            }
        } else {
            $shipping['total'] = 0;
        }

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid or expired coupon.'], 400);
        }

        $subtotal = $cart->items->sum(fn($item) => $item->price_per_unit * $item->quantity);
        
        // Calculate installation fees for items with mobile van installation
        $installationFee = CartService::calculateInstallationFee($cart);
        
        // Discount applies ONLY to subtotal (NO installation, NO shipping)
        $discountableAmount = $subtotal;
        $shippingCost = $shipping['total'] ?? 0;
        
        if ($coupon->min_order_amount && $discountableAmount < $coupon->min_order_amount) {
            return response()->json(['message' => 'Minimum order amount required: ' . $coupon->min_order_amount], 400);
        }

        $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->count();
        $usedByCurrent = CouponUsage::where('coupon_id', $coupon->id)
            ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id()))
            ->when(!Auth::check(), fn($q) => $q->where('session_id', session()->getId()))
            ->count();

        if ($coupon->usage_limit && $totalUsed >= $coupon->usage_limit) {
            return response()->json(['message' => 'Coupon usage limit reached.'], 400);
        }

        if ($coupon->usage_limit_per_user && $usedByCurrent >= $coupon->usage_limit_per_user) {
            return response()->json(['message' => 'You have already used this coupon.'], 400);
        }

        $discount = 0;

        switch ($coupon->type) {
            case 'free_shipping':
                $discount = 0;
                $shippingCost = 0;
                $total = $subtotal + $installationFee + $shippingCost;
                break;
            case 'discount_amount':
                $discount = min($coupon->value, $discountableAmount);
                $total = max(0, $discountableAmount - $discount) + $installationFee + $shippingCost;
                break;
            case 'discount_percentage':
                $discount = round($discountableAmount * ($coupon->value / 100), 2);
                $total = max(0, $discountableAmount - $discount) + $installationFee + $shippingCost;
                break;
            default:
                $total = $discountableAmount + $installationFee + $shippingCost;
                break;
        }

        $cart->update([
            'applied_coupon' => $coupon->code,
            'discount_amount' => $discount,
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully',
            'coupon_code' => $coupon->code,
            'type' => $coupon->type,
            'discount_value' => $coupon->value,
            'discount_applied' => $discount,
            'shipping_cost' => $shippingCost,
            'cart_subtotal' => $subtotal,
            'cart_total' => $total,
        ]);
    }

    public function remove(Request $request)
    {
        $cart = CartService::getCurrentCart();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        if (!$cart->applied_coupon) {
            return response()->json(['message' => 'No coupon applied.'], 400);
        }

        // Recalculate original totals without discount
        $subtotal = $cart->items->sum(fn($item) => $item->price_per_unit * $item->quantity);
        
        // Recalculate shipping (in case it was free shipping coupon)
        $shipping = [];
        if ($cart->area_id) {
            $shipping = ShippingCalculatorService::calculate($cart, 5);
            $shippingCost = !empty($shipping['error']) ? 0 : ($shipping['total'] ?? 0);
        } else {
            $shippingCost = 0;
        }
        
        // Calculate installation fees
        $installationFee = CartService::calculateInstallationFee($cart);
        
        // Total = subtotal + installation + shipping (no discount)
        $total = $subtotal + $installationFee + $shippingCost;

        $cart->update([
            'applied_coupon' => null,
            'discount_amount' => 0,
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon removed successfully',
            'cart_subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'installation_fee' => $installationFee,
            'cart_total' => $total,
        ]);
    }

}
