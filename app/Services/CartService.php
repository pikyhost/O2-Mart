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
                    // Check if tyre has buy 3 get 1 free offer
                    $buyable = $existingItem->buyable;
                    $isOfferActive = ($buyable instanceof \App\Models\Tyre) && ($buyable->buy_3_get_1_free ?? false);
                    $paidQuantity = $isOfferActive && $newQuantity >= 3 
                        ? $newQuantity - 1 
                        : $newQuantity;
                    
                    $existingItem->update([
                        'quantity' => $newQuantity,
                        'price_per_unit' => $item->price_per_unit,
                        'subtotal' => $paidQuantity * $item->price_per_unit,
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
            $validAreaId = \App\Models\Area::first()?->id ?? null;
            if ($validAreaId) {
                $cart->update(['area_id' => $validAreaId]);
            }
        }

        // Calculate items total (same as cart menu)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        
        // Use same subtotal calculation as cart-menu: total / (1 + vatPercent)
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            // Calculate paid quantity for buy 3 get 1 free (tyres only)
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit; // Including VAT
        }
        $subtotal = $menuTotal / (1 + $vatPercent);

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

        // 🟢 Get actual cart-menu values
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            // Calculate paid quantity for buy 3 get 1 free (tyres only)
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit; // Including VAT
        }
        $menuSubtotal = $menuTotal / (1 + $vatPercent);
        $menuVatAmount = $menuTotal - $menuSubtotal;
        
        // Use menu subtotal as the main subtotal
        $subtotal = $menuSubtotal;
        
        // 🟢 VAT = cart-menu total - cart-menu subtotal
        $vat = round($menuTotal - $menuSubtotal, 2);
        
        // Recalculate coupon discount if applied
        $discount = 0;
        if ($cart->applied_coupon) {
            $coupon = \App\Models\Coupon::where('code', $cart->applied_coupon)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
                
            if ($coupon) {
                $totalBeforeDiscount = $subtotal + $shippingCost + $installationFee + $vat;
                switch ($coupon->type) {
                    case 'discount_amount':
                        $discount = min($coupon->value, $totalBeforeDiscount);
                        break;
                    case 'discount_percentage':
                        $discount = round($totalBeforeDiscount * ($coupon->value / 100), 2);
                        break;
                    case 'free_shipping':
                        $discount = $shippingCost;
                        break;
                }
                
                // Update cart with recalculated discount
                $cart->update(['discount_amount' => $discount]);
            }
        }
        
        // Total = SubTotal + Shipping + Installation + VAT - Discount
        $totalBeforeDiscount = $subtotal + $shippingCost + $installationFee + $vat;
        $total = max(0, $totalBeforeDiscount - $discount);

        $deliveryOnly = [];
        $withInstallation = [];
        $installationCenter = [];

        foreach ($cart->items->loadMissing(['buyable', 'mobileVan', 'installationCenter']) as $item) {
            $buyable = $item->buyable;
            $type = $item->shipping_option ?? 'delivery_only';

            // Calculate item subtotal without VAT (same as cart), accounting for buy 3 get 1 free (tyres only)
            $priceWithoutVat = $item->price_per_unit / (1 + $vatPercent);
            $isOfferActive = ($buyable instanceof \App\Models\Tyre) && ($buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $itemSubtotal = $paidQuantity * $priceWithoutVat;
            
            $entry = [
                'name' => $buyable->name
                    ?? ($buyable->product_name ?? null)
                    ?? ($buyable->title ?? ''),
                'quantity' => $item->quantity,
                'price' => floatval($item->price_per_unit),
                'subtotal' => number_format($itemSubtotal, 2),
            ];

            if ($type === 'with_installation') {
                $entry['scheduled'] = [
                    'van_id' => $item->mobile_van_id,
                    'date' => $item->installation_date,
                    'location' => $item->mobileVan ? 
                        ($item->mobileVan->name . ' - ' . $item->mobileVan->location . ' - ' . ($item->installation_date ? \Carbon\Carbon::parse($item->installation_date)->format('D M j Y') : '')) : null,
                ];
                $withInstallation[] = $entry;
            } elseif ($type === 'installation_center') {
                $entry['scheduled'] = [
                    'center_id' => $item->installation_center_id,
                    'date' => $item->installation_date,
                    'location' => $item->installationCenter ? 
                        ($item->installationCenter->name . ' - ' . $item->installationCenter->location . ' - ' . ($item->installation_date ? \Carbon\Carbon::parse($item->installation_date)->format('D M j Y') : '')) : null,
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
                'items_subtotal' => (float) $subtotal,
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
