<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Merge guest cart (via session_id) into user cart
     */
    public static function mergeSessionCartWithUserCart(?string $sessionId): void
    {
        $user = Auth::user();

        if (!$user || !$sessionId) {
            return;
        }

        $sessionCart = Cart::with('items')->where('session_id', $sessionId)->first();
        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        if ($sessionCart) {
            foreach ($sessionCart->items as $item) {
                $existingItem = $userCart->items()
                    ->where('buyable_type', $item->buyable_type)
                    ->where('buyable_id', $item->buyable_id)
                    ->first();

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $item->quantity;
                    $existingItem->update([
                        'quantity' => $newQuantity,
                        'price_per_unit' => $item->price_per_unit,
                        'subtotal' => $newQuantity * $item->price_per_unit,
                    ]);
                } else {
                    $userCart->items()->create([
                        'buyable_type' => $item->buyable_type,
                        'buyable_id' => $item->buyable_id,
                        'quantity' => $item->quantity,
                        'price_per_unit' => $item->price_per_unit,
                        'subtotal' => $item->subtotal,
                    ]);
                }
            }

            // Clean up session cart
            CartItem::where('cart_id', $sessionCart->id)->delete();
            $sessionCart->delete();
        }
    }

    public static function generateCartSummary(Cart $cart): array
    {
        if (!$cart->area_id) {
            $cart->update(['area_id' => 3]);
        }

        // Simple calculation: if buy_3_get_1_free and quantity >= 4, pay for quantity - 1
        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->buyable && $item->buyable->buy_3_get_1_free && $item->quantity >= 4) {
                $subtotal += $item->price_per_unit * ($item->quantity - 1);
            } else {
                $subtotal += $item->price_per_unit * $item->quantity;
            }
        }

        // 🟢 Installation Fees (only for with_installation, not installation_center)
        $installationGroups = [];
        foreach ($cart->items as $item) {
            if ($item->shipping_option === 'with_installation') {
                $key = $item->mobile_van_id . '_' . $item->installation_date;
                $installationGroups[$key] = true;
            }
        }
        $installationFeeValue = \App\Models\ShippingSetting::first()?->installation_fee ?? 200;
        $installationFee = count($installationGroups) * $installationFeeValue;

        // 🟢 Shipping Cost (without VAT)
        $monthlyShipments = auth()->check() ? (auth()->user()->shipment_count ?? 20) : 5;
        $shipping = ShippingCalculatorService::calculate($cart, $monthlyShipments);
        $shippingCost = !empty($shipping['error']) ? 0 : ($shipping['total'] ?? 0);
        $breakdown = !empty($shipping['error']) ? [] : ($shipping['breakdown'] ?? []);

        // 🟢 Calculate discountable amount (subtotal ONLY, exclude installation)
        $discountableAmount = $subtotal;
        
        // 🟢 Recalculate discount if coupon is applied
        $discount = 0;
        $originalShippingCost = $shippingCost;
        if ($cart->applied_coupon) {
            $coupon = \App\Models\Coupon::where('code', $cart->applied_coupon)->first();
            if ($coupon) {
                switch ($coupon->type) {
                    case 'discount_percentage':
                        $discount = round($discountableAmount * ($coupon->value / 100), 2);
                        break;
                    case 'discount_amount':
                        $discount = min($coupon->value, $discountableAmount);
                        break;
                    case 'free_shipping':
                        $discount = 0;
                        $shippingCost = 0;
                        break;
                }
            }
        }
        
        // 🟢 Apply discount ONLY to subtotal (not installation)
        $discountableAfterDiscount = max(0, $discountableAmount - $discount);
        
        // 🟢 Update cart with recalculated discount
        if ($cart->applied_coupon && $cart->discount_amount != $discount) {
            $cart->update(['discount_amount' => $discount]);
        }
        
        // 🟢 Total = (subtotal - discount) + installation + shipping
        $totalAfterDiscount = $discountableAfterDiscount + $installationFee + $shippingCost;
        
        // 🟢 VAT = total after discount
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $vat = round($totalAfterDiscount * $vatPercent, 2);

        // 🟢 Final Total with VAT
        $total = $totalAfterDiscount + $vat;

        $deliveryOnly = [];
        $withInstallation = [];
        $installationCenter = [];

        foreach ($cart->items->loadMissing(['buyable', 'mobileVan', 'installationCenter']) as $item) {
            $buyable = $item->buyable;
            $type = $item->shipping_option ?? 'delivery_only';

            $entry = [
                'name' => $buyable->name
                    ?? ($buyable->product_name ?? null)
                    ?? ($buyable->title ?? ''),
                'quantity' => $item->quantity,
                'price' => floatval($item->price_per_unit),
                'subtotal' => $item->subtotal,
            ];

            if ($type === 'with_installation') {
                $entry['scheduled'] = [
                    'van_id' => $item->mobile_van_id,
                    'date' => $item->installation_date,
                    'location' => optional($item->mobileVan)->location,
                ];
                $withInstallation[] = $entry;
            } elseif ($type === 'installation_center') {
                $entry['scheduled'] = [
                    'center_id' => $item->installation_center_id,
                    'date' => $item->installation_date,
                    'location' => optional($item->installationCenter)->location,
                ];
                $installationCenter[] = $entry;
            } else {
                $deliveryOnly[] = $entry;
            }
        }

        return [
            'delivery_only' => $deliveryOnly,
            'with_installation' => $withInstallation,
            'installation_center' => $installationCenter,
            'totals' => [
                'items_subtotal' => $subtotal,
                'shipping' => $shippingCost,
                'installation' => $installationFee,
                'discount' => $discount,
                'vat' => $vat,
                'total' => round($total, 2),
            ],
            'shipping_breakdown' => $breakdown,
            'applied_coupon' => $cart->applied_coupon,
            'coupon_applied' => !empty($cart->applied_coupon),
            'coupon_savings' => $discount,
            'coupon_type' => $cart->applied_coupon ? (\App\Models\Coupon::where('code', $cart->applied_coupon)->first()?->type ?? null) : null,
        ];
    }



    public static function calculateInstallationFee(Cart $cart): float
    {
        $installationGroups = [];

        foreach ($cart->items as $item) {
            if ($item->shipping_option === 'with_installation') {
                $key = $item->mobile_van_id . '_' . $item->installation_date;
                $installationGroups[$key] = true;
            }
        }

        $installationFee = \App\Models\ShippingSetting::first()?->installation_fee ?? 200;
        return count($installationGroups) * $installationFee;
    }

    /**
     * Get current cart (user or guest)
     */
    public static function getCurrentCart(): Cart
    {
        $user = auth('sanctum')->user();
        $sessionId = request()->header('X-Session-ID') ?? session()->getId();

        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        } elseif ($sessionId) {
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        throw new \Exception('Unauthenticated or session ID missing.');
    }
}
