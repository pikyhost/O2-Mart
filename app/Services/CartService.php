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
        // No default area - shipping cost will be 0 until user selects area

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

        // 🟢 Shipping Cost (use saved shipping_cost from cart)
        $shippingCost = $cart->shipping_cost ?? 0;
        $breakdown = [];
        
        // Get breakdown if shipping cost exists and area is selected
        if ($shippingCost > 0 && $cart->area_id) {
            $monthlyShipments = auth()->check() ? (auth()->user()->shipment_count ?? 20) : 5;
            $shipping = ShippingCalculatorService::calculate($cart, $monthlyShipments);
            $breakdown = !empty($shipping['error']) ? [] : ($shipping['breakdown'] ?? []);
            // Don't override the saved shipping cost - just get the breakdown
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
        
        // Recalculate coupon discount if applied (same logic as checkout)
        $discount = 0;
        if ($cart->applied_coupon) {
            $coupon = \App\Models\Coupon::where('code', $cart->applied_coupon)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
                
            if ($coupon) {
                switch ($coupon->type) {
                    case 'discount_amount':
                        $discount = min($coupon->value, $subtotal);
                        break;
                    case 'discount_percentage':
                        $discount = round($subtotal * ($coupon->value / 100), 2);
                        break;
                    case 'free_shipping':
                        $discount = $shippingCost;
                        break;
                }
                
                // Update cart with recalculated discount
                $cart->update(['discount_amount' => $discount]);
            }
        }
        
        // Total calculation exactly same as checkout
        // In checkout: $total = $cartSummary['totals']['total'] - $discountAmount;
        // Where cartSummary total = subtotal + shipping + installation + vat
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
                'items_subtotal' => (float) number_format($subtotal, 2, '.', ''),
                'shipping' => (float) number_format($shippingCost, 2, '.', ''),
                'installation' => $installationFee,
                'discount' => $discount,
                'vat' => $vat,
                'total' => (float) round($total, 2),
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
        $shippingSetting = \App\Models\ShippingSetting::first();
        
        // Check for rim items with installation_center shipping option
        $hasRimInstallationCenter = $cart->items->contains(function ($item) {
            return $item->buyable_type === 'rim' && 
                   $item->shipping_option === 'installation_center';
        });
        
        if ($hasRimInstallationCenter) {
            return $shippingSetting?->rim_installation_center_fee ?? 0;
        }
        
        // Check for regular installation (with_installation)
        $hasInstallation = $cart->items->contains('shipping_option', 'with_installation');
        
        if ($hasInstallation) {
            return $shippingSetting?->installation_fee ?? 200;
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
