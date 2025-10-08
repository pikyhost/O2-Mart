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
            if (!\App\Models\Area::find($request->area_id)) {
                return response()->json(['message' => 'Invalid area ID.'], 400);
            }
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

        // Calculate items total and subtotal (same as cart menu logic)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $itemsTotal = $cart->items->sum(fn($item) => $item->price_per_unit * $item->quantity);
        $vatAmount = $itemsTotal * $vatPercent;
        $subtotal = $itemsTotal - $vatAmount;
        
        // Calculate installation fees
        $installationFee = CartService::calculateInstallationFee($cart);
        
        // Calculate shipping (only for delivery_only items)
        $hasDeliveryOnly = $cart->items->contains('shipping_option', 'delivery_only');
        $shippingCost = 0;
        if ($hasDeliveryOnly && $cart->area_id) {
            $shippingCost = $shipping['total'] ?? 0;
        }
        
        // Discount applies ONLY to subtotal
        $discountableAmount = $subtotal;
        
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
                break;
            case 'discount_amount':
                $discount = min($coupon->value, $discountableAmount);
                break;
            case 'discount_percentage':
                $discount = round($discountableAmount * ($coupon->value / 100), 2);
                break;
            default:
                $discount = 0;
                break;
        }
        
        // Total = SubTotal + Shipping + Installation + VAT - Discount
        $totalBeforeVat = max(0, $subtotal - $discount) + $shippingCost + $installationFee;
        $vat = round($totalBeforeVat * $vatPercent, 2);
        $total = $totalBeforeVat + $vat;

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
            'cart' => [
                'items_total' => (float) $itemsTotal,
                'subtotal' => (float) $subtotal,
                'installation_fee' => (float) $installationFee,
                'discount' => (float) $discount,
                'total' => (float) $total,
                'shipping_cost' => (float) $shippingCost,
                'vat' => (float) $vat,
                'checkout_total' => (float) $total,
                'coupon_code' => $coupon->code,
                'coupon_applied' => true,
                'coupon_savings' => $discount,
            ]
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

        // Recalculate totals without discount (same as cart menu logic)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $itemsTotal = $cart->items->sum(fn($item) => $item->price_per_unit * $item->quantity);
        $vatAmount = $itemsTotal * $vatPercent;
        $subtotal = $itemsTotal - $vatAmount;
        
        // Calculate installation fees
        $installationFee = CartService::calculateInstallationFee($cart);
        
        // Recalculate shipping (only for delivery_only items)
        $hasDeliveryOnly = $cart->items->contains('shipping_option', 'delivery_only');
        $shippingCost = 0;
        if ($hasDeliveryOnly && $cart->area_id) {
            $shipping = ShippingCalculatorService::calculate($cart, 5);
            $shippingCost = !empty($shipping['error']) ? 0 : ($shipping['total'] ?? 0);
        }
        
        // Total = SubTotal + Shipping + Installation + VAT (no discount)
        $totalBeforeVat = $subtotal + $shippingCost + $installationFee;
        $vat = round($totalBeforeVat * $vatPercent, 2);
        $total = $totalBeforeVat + $vat;

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
            'cart' => [
                'items_total' => (float) $itemsTotal,
                'subtotal' => (float) $subtotal,
                'installation_fee' => (float) $installationFee,
                'discount' => 0.0,
                'total' => (float) $total,
                'shipping_cost' => (float) $shippingCost,
                'vat' => (float) $vat,
                'checkout_total' => (float) $total,
                'coupon_code' => null,
                'coupon_applied' => false,
                'coupon_savings' => 0.0,
            ]
        ]);
    }

}
