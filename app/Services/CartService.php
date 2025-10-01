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

        // Calculate items total (same as cart menu)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $itemsTotal = 0;
        foreach ($cart->items as $item) {
            if ($item->buyable && $item->buyable->buy_3_get_1_free && $item->quantity >= 4) {
                $itemsTotal += $item->price_per_unit * ($item->quantity - 1);
            } else {
                $itemsTotal += $item->price_per_unit * $item->quantity;
            }
        }
        
        // Subtotal = sum of item subtotals (without VAT) - same as cart endpoints
        $subtotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $priceWithoutVat = $item->price_per_unit - ($item->price_per_unit * $vatPercent);
            $subtotal += $item->quantity * $priceWithoutVat;
        }

        // 🟢 Installation Fees (only once if any item has with_installation)
        $installationFee = 0;
        $hasInstallation = $cart->items->contains('shipping_option', 'with_installation');
        if ($hasInstallation) {
            $installationFeeValue = \App\Models\ShippingSetting::first()?->installation_fee ?? 200;
            $installationFee = $installationFeeValue;
        }

        // 🟢 Shipping Cost (only for delivery_only option)
        $shippingCost = 0;
        $breakdown = [];
        $hasDeliveryOnly = $cart->items->contains('shipping_option', 'delivery_only');
        
        if ($hasDeliveryOnly) {
            $monthlyShipments = auth()->check() ? (auth()->user()->shipment_count ?? 20) : 5;
            $shipping = ShippingCalculatorService::calculate($cart, $monthlyShipments);
            $shippingCost = !empty($shipping['error']) ? 0 : ($shipping['total'] ?? 0);
            $breakdown = !empty($shipping['error']) ? [] : ($shipping['breakdown'] ?? []);
        }

        // 🟢 Calculate discountable amount (subtotal ONLY, exclude installation)
        $discountableAmount = $subtotal;
        
        // 🟢 Recalculate discount if coupon is applied
        $discount = 0;
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
        
        // 🟢 Calculate menu total and VAT (same as cart menu logic)
        $menuTotal = $itemsTotal; // This includes VAT already
        $menuVat = $menuTotal * $vatPercent;
        $menuSubtotal = $menuTotal - $menuVat;
        
        // 🟢 Total = SubTotal + Shipping + Installation + VAT amount
        $totalBeforeVat = $discountableAfterDiscount + $shippingCost + $installationFee;
        $vat = $menuVat; // Use menu VAT calculation
        $total = $totalBeforeVat + $vat;

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
        $hasInstallation = $cart->items->contains('shipping_option', 'with_installation');
        
        if ($hasInstallation) {
            return \App\Models\ShippingSetting::first()?->installation_fee ?? 200;
        }
        
        return 0;
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
