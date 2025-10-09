<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InstallerShop;
use App\Models\MobileVanService;
use App\Models\Product;
use App\Models\VinRecord;
use App\Services\CartService;
use App\Services\ShippingCalculatorService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        \Log::info('Cart Add Request Data', [
            'all_data' => $request->all(),
            'has_cart_payload' => $request->has('cart_payload'),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        // Handle cart payload for tyre groups
        if ($request->has('cart_payload')) {
            return $this->addTyreGroup($request);
        }

        $request->validate([
            'buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id'   => 'required|integer',
            'quantity'     => 'nullable|integer|min:1',
        ]);

        $modelClass = match ($request->buyable_type) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery'   => \App\Models\Battery::class,
            'tyre'      => \App\Models\Tyre::class,
            'rim'       => \App\Models\Rim::class,
        };

        $itemModel = $modelClass::find($request->buyable_id);
        if (!$itemModel) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $morphType = app($modelClass)->getMorphClass();

        // Get price with proper decimal precision
        $priceString = $itemModel->discounted_price 
            ?? $itemModel->price_vat_inclusive 
            ?? $itemModel->price_including_vat 
            ?? $itemModel->regular_price 
            ?? '0';
        
        $price = (float) $priceString;
        
        \Log::info('Cart Add Price Debug', [
            'tyre_id' => $itemModel->id,
            'discounted_price' => $itemModel->discounted_price,
            'price_vat_inclusive' => $itemModel->price_vat_inclusive,
            'price_including_vat' => $itemModel->price_including_vat,
            'regular_price' => $itemModel->regular_price,
            'selected_price_string' => $priceString,
            'converted_price_float' => $price,
            'price_type' => gettype($priceString)
        ]);
        $quantity = $request->quantity ?? 1;
        $isOfferActive = ($itemModel instanceof \App\Models\Tyre) && ($itemModel->buy_3_get_1_free ?? false);

        $cart = CartService::getCurrentCart();

        $existingItem = $cart->items()
            ->where('buyable_type', $morphType)
            ->where('buyable_id', $itemModel->id)
            ->first();

        \Log::info('Tyre Offer Debug', [
            'buy_3_get_1_free' => $isOfferActive,
            'class' => get_class($itemModel),
            'id' => $itemModel->id,
            'title' => $itemModel->title ?? null,
            'quantity_added' => $quantity,
        ]);

        if ($existingItem) {
            $newTotalQuantity = $existingItem->quantity + $quantity;
            
            // Calculate paid quantity: if >= 3, pay for quantity - 1
            $paidQuantity = $isOfferActive && $newTotalQuantity >= 3 
                ? $newTotalQuantity - 1 
                : $newTotalQuantity;

            $existingItem->update([
                'quantity' => $newTotalQuantity, 
                'subtotal' => $price * $paidQuantity, 
            ]);

        } else {
            // Calculate paid quantity: if >= 3, pay for quantity - 1
            $paidQuantity = $isOfferActive && $quantity >= 3 
                ? $quantity - 1 
                : $quantity;

            $cartItem = $cart->items()->create([
                'buyable_type'   => $morphType,  
                'buyable_id'     => $request->buyable_id,
                'quantity'       => $quantity,
                'price_per_unit' => $price,
                'subtotal'       => $price * $paidQuantity,
            ]);
            
            \Log::info('Cart Item Created', [
                'stored_price_per_unit' => $cartItem->price_per_unit,
                'stored_subtotal' => $cartItem->subtotal,
                'expected_price' => $price,
                'expected_subtotal' => $price * $paidQuantity
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart.',
            'session_id' => session()->getId(),
        ]);
    }


    public function remove(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id'   => 'required|integer',
        ]);

        $modelClass = match ($request->buyable_type) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery'   => \App\Models\Battery::class,
            'tyre'      => \App\Models\Tyre::class,
            'rim'       => \App\Models\Rim::class,
        };

        $cart = CartService::getCurrentCart();

        $deleted = $cart->items()
            ->whereIn('buyable_type', [$request->buyable_type, $modelClass]) 
            ->where('buyable_id', $request->buyable_id)
            ->delete();

        return response()->json([
            'message' => $deleted ? 'Product removed from cart.' : 'Product not found in cart.',
            'session_id' => session()->getId(),
        ]);
    }

    public function getRecommendations(Request $request)
    {
        $request->validate([
            'vin' => 'nullable|string|size:17',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
        ]);

        $cart = CartService::getCurrentCart()->load('items');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        $excludedItems = $cart->items->map(function ($item) {
            return $item->buyable_type . ':' . $item->buyable_id;
        })->toArray();

        $carModel = null;

        if ($request->filled('vin')) {
            $vin = VinRecord::where('vin', $request->vin)->first();
            if ($vin) {
                $carModel = $vin->carModel;
            }
        } elseif ($request->filled(['make', 'model', 'year'])) {
            $carModel = CarModel::whereHas('make', function ($q) use ($request) {
                    $q->where('name', $request->make);
                })
                ->where('name', $request->model)
                ->where('year_from', '<=', $request->year)
                ->where(function ($q) use ($request) {
                    $q->whereNull('year_to')->orWhere('year_to', '>=', $request->year);
                })->first();
        }

        if (!$carModel) {
            return response()->json(['message' => 'Car model not found'], 404);
        }

        $compatibleItems = [];

        foreach (['AutoPart', 'Battery', 'Tyre', 'Rim'] as $type) {
            $modelClass = "\\App\\Models\\$type";

            $items = $modelClass::whereHas('compatibleCarModels', function ($q) use ($carModel) {
                $q->where('car_model_id', $carModel->id);
            })->get();

            foreach ($items as $item) {
                $key = $modelClass . ':' . $item->id;

                if (!in_array($key, $excludedItems)) {
                    $compatibleItems[] = [
                        'type' => $type,
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->discounted_price ?? $item->price_including_vat,
                        'image' => $this->resolveImage($item),
                    ];
                }
            }
        }

        return response()->json([
            'car_model' => [
                'make' => $carModel->make->name,
                'model' => $carModel->name,
                'year_from' => $carModel->year_from,
                'year_to' => $carModel->year_to,
            ],
            'session_id' => session()->getId(),
            'recommendations' => array_slice($compatibleItems, 0, 5),
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id'   => 'required|integer',
            'quantity'     => 'required|integer|min:1',
            'vin'          => 'nullable|string|size:17',
            'make'         => 'nullable|string',
            'model'        => 'nullable|string',
            'year'         => 'nullable|integer|min:1900|max:' . (now()->year + 1),
        ]);

        $cart = CartService::getCurrentCart()->load('items.buyable');
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $modelClass = match ($request->buyable_type) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery'   => \App\Models\Battery::class,
            'tyre'      => \App\Models\Tyre::class,
            'rim'       => \App\Models\Rim::class,
        };
        $morphType = app($modelClass)->getMorphClass();

        $item = $cart->items()
            ->where('buyable_id', $request->buyable_id)
            ->whereIn('buyable_type', [$morphType, $modelClass]) 
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $carModel = null;
        if ($request->filled('vin')) {
            $vin = \App\Models\VinRecord::where('vin', $request->vin)->first();
            $carModel = $vin?->carModel;
        } elseif ($request->filled(['make','model','year'])) {
            $carModel = \App\Models\CarModel::whereHas('make', fn($q)=>$q->where('name',$request->make))
                ->where('name', $request->model)
                ->where('year_from', '<=', $request->year)
                ->where(fn($q)=>$q->whereNull('year_to')->orWhere('year_to','>=',$request->year))
                ->first();
        }

        $isCompatible = true;
        if ($carModel && method_exists($item->buyable, 'compatibleCarModels')) {
            $isCompatible = $item->buyable->compatibleCarModels()
                ->where('car_model_id', $carModel->id)
                ->exists();
        }
        if (!$isCompatible) {
            return response()->json(['message' => 'This product is not compatible with the selected vehicle.'], 400);
        }

        // Check if this is a tyre with buy 3 get 1 free offer
        $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
        
        // Calculate paid quantity: if >= 3, pay for quantity - 1
        $paidQuantity = $isOfferActive && $request->quantity >= 3 
            ? $request->quantity - 1 
            : $request->quantity;

        $item->update([
            'quantity' => $request->quantity,
            'subtotal' => $item->price_per_unit * $paidQuantity,
        ]);

        return response()->json(['message' => 'Quantity updated successfully.']);
    }


    public function updateShippingOption(Request $request)
    {
        $request->validate([
            'buyable_type'           => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id'             => 'required|integer',
            'shipping_option'        => 'required|string|in:delivery_only,with_installation,installation_center',
            'mobile_van_id'          => 'nullable|integer',
            'installation_center_id' => 'nullable|integer',
            'installation_date'      => 'nullable|date',
        ]);

        $modelClass = match ($request->buyable_type) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery'   => \App\Models\Battery::class,
            'tyre'      => \App\Models\Tyre::class,
            'rim'       => \App\Models\Rim::class,
        };
        $morphType = app($modelClass)->getMorphClass();

        $cart = CartService::getCurrentCart();

        $item = $cart->items()
            ->where('buyable_id', $request->buyable_id)
            ->whereIn('buyable_type', [$morphType, $modelClass]) 
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $item->update([
            'shipping_option'        => $request->shipping_option,
            'mobile_van_id'          => $request->mobile_van_id,
            'installation_center_id' => $request->center_id ?? null,
            'installation_date'      => $request->installation_date,
        ]);

        // Recalculate cart totals after shipping option change
        $cart = $cart->load('items.buyable');
        $cartTotal = 0;
        
        foreach ($cart->items as $cartItem) {
            if ($cartItem->buyable) {
                $cartTotal += $cartItem->subtotal;
            }
        }
        
        $installationFee = CartService::calculateInstallationFee($cart);
        $shippingCost = $cart->shipping_cost ?? 0;
        $discountableAmount = $cartTotal + $installationFee;
        
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
                switch ($coupon->type) {
                    case 'discount_amount':
                        $discount = min($coupon->value, $discountableAmount);
                        break;
                    case 'discount_percentage':
                        $discount = round($discountableAmount * ($coupon->value / 100), 2);
                        break;
                    case 'free_shipping':
                        $discount = $shippingCost;
                        break;
                }
            }
        }
        
        $cartPageTotal = max(0, $discountableAmount - $discount);
        $checkoutTotal = $cartPageTotal + $shippingCost;
        
        $cart->update([
            'subtotal' => $cartTotal,
            'discount_amount' => $discount,
            'total' => $checkoutTotal
        ]);
        
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $vat = round($checkoutTotal * $vatPercent, 2);
        
        return response()->json([
            'message'    => 'Shipping option updated successfully.',
            'session_id' => session()->getId(),
            'cart' => [
                'items_total'      => (float) $cartTotal,
                'installation_fee' => (float) $installationFee,
                'discount'         => (float) $discount,
                'total'            => (float) $cartPageTotal,
                'shipping_cost'    => (float) $shippingCost,
                'vat'              => (float) $vat,
                'checkout_total'   => (float) round($checkoutTotal + $vat, 2),
            ]
        ]);
    }

    public function getCart()
    {
        $cart = CartService::getCurrentCart()->load('items.buyable');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $cartTotal = 0;

        foreach ($cart->items as $item) {
            if (!$item->buyable) {
                continue;
            }
            // Use subtotal which already accounts for buy 3 get 1 free
            $cartTotal += $item->subtotal;
        }

        // Calculate installation fee only once if any item has "with_installation" shipping option
        $installationFee = 0;
        $shippingSetting = \App\Models\ShippingSetting::first();
        if ($shippingSetting) {
            $hasInstallation = $cart->items->contains('shipping_option', 'with_installation');
            if ($hasInstallation) {
                $installationFee = $shippingSetting->installation_fee ?? 0;
            }
        }

        $shippingCost = $cart->shipping_cost ?? 0;
        
        // Cart page total = items total + installation fees
        $cartPageTotal = $cartTotal + $installationFee;
        
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
                $totalBeforeDiscount = $cartPageTotal + $shippingCost;
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
        
        // Apply discount to total
        $cartPageTotalAfterDiscount = max(0, $cartPageTotal - $discount);
        
        // Checkout total = cart total after discount + shipping
        $checkoutTotal = $cartPageTotalAfterDiscount + $shippingCost;
        
        // Subtotal = sum of item subtotals (without VAT)
        $subtotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $priceWithoutVat = $item->price_per_unit - ($item->price_per_unit * $vatPercent);
            // Calculate paid quantity for buy 3 get 1 free (tyres only)
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $subtotal += $paidQuantity * $priceWithoutVat;
        }
        
        // Update cart totals
        $cart->update([
            'subtotal' => $subtotal,
            'total' => $checkoutTotal
        ]);

        $vat = round($checkoutTotal * $vatPercent, 2);

        return response()->json([
            'message' => 'Cart loaded successfully',
            'session_id' => session()->getId(),
            'cart' => [
                'items' => $cart->items->filter(fn($item) => $item->buyable !== null)->map(function ($item) use ($vatPercent) {
                    $buyable = $item->buyable;

                    // Calculate paid quantity for buy 3 get 1 free (tyres only)
                    $isOfferActive = ($buyable instanceof \App\Models\Tyre) && ($buyable->buy_3_get_1_free ?? false);
                    $paidQuantity = $isOfferActive && $item->quantity >= 3 
                        ? $item->quantity - 1 
                        : $item->quantity;
                    
                    \Log::info('Individual Item Subtotal Debug', [
                        'item_id' => $buyable->id,
                        'is_tyre' => $buyable instanceof \App\Models\Tyre,
                        'buy_3_get_1_free' => $buyable->buy_3_get_1_free ?? false,
                        'is_offer_active' => $isOfferActive,
                        'quantity' => $item->quantity,
                        'paid_quantity' => $paidQuantity,
                        'price_per_unit' => $item->price_per_unit,
                        'vat_percent' => $vatPercent,
                        'price_without_vat' => $item->price_per_unit - ($item->price_per_unit * $vatPercent),
                        'calculated_subtotal' => $paidQuantity * ($item->price_per_unit - ($item->price_per_unit * $vatPercent))
                    ]);
                    
                    $itemData = [
                        'type' => class_basename($item->buyable_type),
                        'id' => $buyable->id,
                        'name' => $this->resolveName($buyable),
                        'price_per_unit' => (float) $item->price_per_unit, // Including VAT
                        'quantity' => $item->quantity,
                        'sub' => (float) ($paidQuantity * ($item->price_per_unit - ($item->price_per_unit * $vatPercent))), // VAT-exclusive subtotal with paid quantity
                        'image' => $this->resolveImage($buyable),
                        'shipping_option' => $item->shipping_option,
                        'mobile_van_id' => $item->mobile_van_id,
                        'installation_center_id' => $item->installation_center_id,
                        'installation_date' => $item->installation_date,
                    ];
                    
                    \Log::info('Cart API Response Item', [
                        'item_id' => $buyable->id,
                        'db_price_per_unit' => $item->price_per_unit,
                        'db_subtotal' => $item->subtotal,
                        'response_price_per_unit' => $itemData['price_per_unit'],
                        'response_subtotal' => $itemData['sub']
                    ]);
                    
                    return $itemData;
                }),
                'items_total'         => (float) $cartTotal,
                'subtotal'            => (float) $subtotal,
                'installation_fee'    => (float) $installationFee,
                'discount'            => (float) $discount,
                'total'               => (float) $cartPageTotalAfterDiscount,
                'shipping_cost'       => (float) $shippingCost,
                'vat'                 => (float) $vat,
                'checkout_total'      => (float) round($checkoutTotal + $vat, 2),
                'coupon_code'         => $cart->applied_coupon,
                'coupon_applied'      => !empty($cart->applied_coupon),
                'coupon_savings'      => $discount,
            ]
        ]);
    }


    public function getCartSummary(Request $request)
    {
        $cart = CartService::getCurrentCart();
        $summary = CartService::generateCartSummary($cart);

        return response()->json([
            'status' => 'success',
            'session_id' => $cart->session_id,
            'summary' => $summary
        ]);
    }
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'monthly_shipments' => 'required|integer|min:0',
            'area_id' => 'required|integer',  
        ]);
        $areaId = $request->area_id;
        if (!\App\Models\Area::find($areaId)) {
            return response()->json([
                'status' => 'error',
                'session_id' => session()->getId(),
                'message' => 'Invalid area ID.'
            ], 400);
        }

        $cart = CartService::getCurrentCart()->load('items.buyable');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        $cart->update(['area_id' => $request->area_id]);

        $shipping = ShippingCalculatorService::calculate($cart, $request->monthly_shipments);

        if (!empty($shipping['error'])) {
            return response()->json([
                'status' => 'error',
                'session_id' => session()->getId(),
                'message' => $shipping['message'] ?? 'Shipping calculation failed.',
            ], 400);
        }

        $total = $cart->subtotal + $shipping['total'];

        $cart->update([
            'shipping_cost' => $shipping['total'],
            'total' => $total,
        ]);

        return response()->json([
            'message' => 'Shipping cost calculated successfully',
            'session_id' => session()->getId(),
            'shipping_cost' => $shipping['total'],
            'cart_total' => $total,
            'breakdown' => $shipping['breakdown'],
        ]);
    }


    public function getShippingBreakdown(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be logged in to view shipping breakdown.'
            ], 401);
        }

        $cart = Cart::with('items.buyable')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'session_id' => session()->getId(),
                'message' => 'Cart is empty.'
            ], 400);
        }

        if (!$cart->area_id) {
            $areaId = $request->input('area_id');

            if (!$areaId || !\App\Models\Area::find($areaId)) {
                return response()->json([
                    'status' => 'error',
                    'session_id' => session()->getId(),                    
                    'message' => 'Area ID is missing or invalid. Please provide area_id.'
                ], 400);
            }

            $cart->update(['area_id' => $areaId]);
        }

        $monthlyShipments = $user->shipment_count ?? 20;

        $shipping = ShippingCalculatorService::calculate($cart, $monthlyShipments);

        return response()->json([
            'status' => 'success',
            'session_id' => session()->getId(),
            'data' => $shipping
        ]);
    }


    public function getMobileVanServices(Request $request)
    {
        $location    = $request->input('location');
        $buyableType = $request->input('buyable_type');

        $normalizedType = match (strtolower(class_basename($buyableType))) {
            'autopart' => 'auto_part',
            'battery'  => 'battery',
            'tyre'     => 'tyre',
            'rim'      => 'rim',
            default    => match (strtolower($buyableType)) {
                'auto_part' => 'auto_part',
                'battery'  => 'battery',
                'tyre'     => 'tyre',
                'rim'      => 'rim',
                default    => null,
            },
        };

            $query = MobileVanService::query()
            ->with([
                'workingHours' => fn($q) => $q->orderBy('day_id'),
                'branches',
                'productTypes',
            ]);

        if ($normalizedType) {
            $query->whereHas('productTypes', fn($q) => $q->where('name', $normalizedType));
        }

        if ($location) {
            $query->where('location', 'like', '%' . $location . '%');
        }

        $vans = $query->get();

        // $vans->each(fn($van) => $van->makeHidden(['working_hours']));

        return response()->json([
            'status'     => 'success',
            'session_id' => session()->getId(),
            'data'       => $vans, 
        ]);
    }



    public function getInstallerShops(Request $request)
    {
        $location    = $request->input('location');
        $buyableType = $request->input('buyable_type');

        $normalizedType = match (strtolower(class_basename($buyableType))) {
            'autopart' => 'auto_part',
            'battery'  => 'battery',
            'tyre'     => 'tyre',
            'rim'      => 'rim',
            default    => match (strtolower($buyableType)) {
                'auto_part' => 'auto_part',
                'battery'  => 'battery',
                'tyre'     => 'tyre',
                'rim'      => 'rim',
                default    => null,
            },
        };

        $query = InstallerShop::query()->with([
            'workingHours' => fn($q) => $q->orderBy('day_id'),
            'branches',
            'productTypes',
        ]);

        if ($normalizedType) {
            $query->whereHas('productTypes', fn($q) => $q->where('name', $normalizedType));
        }

        if ($location) {
            $query->where('location', 'like', '%'.$location.'%');
        }

        $shops = $query->get();

        $shops->each(function ($shop) use ($request) {
            $shop->append('working_hours_display');
            if ($request->boolean('summary_only')) {
                $shop->makeHidden(['working_hours']);
            }
        });

        return response()->json([
            'status'     => 'success',
            'session_id' => session()->getId(),
            'data'       => $shops,
        ]);
    }





    public function getAvailableInstallationDates(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mobile_van,installer_shop',
            'id' => 'required|integer',
            'days' => 'nullable|integer|min:1|max:30',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');
        $days = $request->input('days', 30);

        $model = $type === 'mobile_van'
            ? \App\Models\MobileVanService::with('workingHours')->find($id)
            : \App\Models\InstallerShop::with('workingHours')->find($id);

        if (!$model) {
            return response()->json(['status' => 'error', 'message' => 'Branch not found.'], 404);
        }

        $workingHours = $model->workingHours->keyBy('day_id'); // day_id: Monday=1 to Sunday=7
        $dates = [];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i);
            $dayId = $date->dayOfWeekIso;

            $wh = $workingHours->get($dayId);
            $isClosed = $wh ? $wh->is_closed : true;

            $dates[] = [
                'date' => $date->toDateString(),
                'day' => $i === 0 ? 'today' : $date->format('l'),
                'is_closed' => (bool) $isClosed,
                'available' => !$isClosed,
            ];
        }


        return response()->json([
            'status' => 'success',
            'session_id' => session()->getId(),
            'data' => $dates,
        ]);
    }

    private function resolveName($buyable): string
    {
        return $buyable->name
            ?? ($buyable->product_name ?? null)
            ?? ($buyable->title ?? '')
            ?? '';
    }


    private function resolveImage($buyable): ?string
    {
        return match (get_class($buyable)) {
            \App\Models\AutoPart::class => $this->normalizePhotoLink($buyable->photo_link)
                ?? $buyable->getAutoPartFeatureImageUrl()
                ?? $buyable->getAutoPartSecondaryImageUrl()
                ?? $this->normalizePhotoLink($buyable->image ?? null)
                ?? $buyable->getFirstMediaUrl('auto_part_feature_image')
                ?? $buyable->getFirstMediaUrl('auto_part_secondary_image')
                ?? $buyable->getFirstMediaUrl('feature')
                ?? ($buyable->getFirstMediaUrl() ?: null),

            \App\Models\Tyre::class => $buyable->tyre_feature_image_url
                ?? $buyable->tyre_secondary_image_url
                ?? $this->normalizePhotoLink($buyable->image)
                ?? ($buyable->getFirstMediaUrl() ?: null),

            \App\Models\Battery::class => $buyable->getFirstMediaUrl('battery_feature_image')
                ?? $buyable->getFirstMediaUrl('battery_secondary_image')
                ?? $buyable->feature_image_url
                ?? $buyable->image_url
                ?? $this->normalizePhotoLink($buyable->photo_link)
                ?? ($buyable->getFirstMediaUrl() ?: null),

            \App\Models\Rim::class => $buyable->rim_feature_image_url
                ?? $buyable->rim_secondary_image_url
                ?? $this->normalizePhotoLink($buyable->photo_link)
                ?? ($buyable->getFirstMediaUrl() ?: null),

            default => ($buyable->getFirstMediaUrl() ?: null),
        };
    }

    private function normalizePhotoLink(?string $photoLink): ?string
    {
        if (!$photoLink || trim($photoLink) === '') return null;
        
        // If it's already a full URL (http/https), return as is
        if (str_starts_with($photoLink, 'http')) {
            return $photoLink;
        }
        
        // Otherwise, treat as storage path
        return asset('storage/' . ltrim($photoLink, '/'));
    }

    public function getCartMenu()
    {
        $cart = CartService::getCurrentCart()->load('items.buyable');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'items' => [],
                'subtotal' => 0,
                'total' => 0
            ]);
        }

        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $items = [];
        $total = 0;

        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;

            // Calculate paid quantity for buy 3 get 1 free (tyres only)
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            
            $itemTotal = $paidQuantity * $item->price_per_unit;
            $total += $itemTotal;

            $items[] = [
                'id' => $item->buyable->id,
                'name' => $this->resolveName($item->buyable),
                'item_price' => (float) $item->price_per_unit, // Including VAT
                'quantity' => $item->quantity,
                'total' => (float) $itemTotal, // Including VAT
                'image' => $this->resolveImage($item->buyable),
            ];
        }

        $vatAmount = $total * $vatPercent;
        $subtotal = $total - $vatAmount;

        return response()->json([
            'items' => $items,
            'subtotal' => (float) $subtotal,
            'total' => (float) $total
        ]);
    }

    public function addTyreGroup(Request $request)
    {
        \Log::info('AddTyreGroup Request Data', [
            'all_data' => $request->all(),
            'has_cart_payload' => $request->has('cart_payload'),
            'has_items' => $request->has('items')
        ]);

        if ($request->has('cart_payload')) {
            $request->merge([
                'items' => $request->input('cart_payload')
            ]);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.buyable_id' => 'required|integer|exists:tyres,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        \Log::info('AddTyreGroup Validated Items', [
            'items' => $request->input('items')
        ]);

        $cart = \App\Services\CartService::getCurrentCart();

        foreach ($request->items as $itemData) {
            $tyre = \App\Models\Tyre::find($itemData['buyable_id']);
            $quantity = $itemData['quantity'];
            $price = $tyre->discounted_price ?? $tyre->price_vat_inclusive ?? $tyre->regular_price ?? 0;
            
            // Calculate paid quantity for buy 3 get 1 free (tyres only)
            $isOfferActive = $tyre->buy_3_get_1_free ?? false;
            $paidQuantity = $isOfferActive && $quantity >= 3 
                ? $quantity - 1 
                : $quantity;

            // Check if item already exists in cart
            $existingItem = $cart->items()
                ->where('buyable_type', $tyre->getMorphClass())
                ->where('buyable_id', $tyre->id)
                ->first();

            if ($existingItem) {
                // Update existing item with new quantity (replace, don't add)
                $existingItem->update([
                    'quantity' => $quantity,
                    'price_per_unit' => $price,
                    'subtotal' => $price * $paidQuantity,
                ]);
            } else {
                // Create new item
                $cart->items()->create([
                    'buyable_type'   => $tyre->getMorphClass(), 
                    'buyable_id'     => $tyre->id,
                    'quantity'       => $quantity,
                    'price_per_unit' => $price,
                    'subtotal'       => $price * $paidQuantity,
                ]);
            }
        }

        return response()->json([
            'status'     => 'success',
            'message'    => 'Tyre group added to cart successfully.',
            'session_id' => session()->getId(),
            'cart'       => $cart->load('items')
        ]);
    }



}

