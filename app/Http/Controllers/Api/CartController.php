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
                        'image' => method_exists($item, 'getAutoPartSecondaryImageUrl')
                            ? $item->getAutoPartSecondaryImageUrl()
                            : null,
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
        $discount = $cart->discount_amount ?? 0;
        $shippingCost = $cart->shipping_cost ?? 0;
        $discountableAmount = $cartTotal + $installationFee;
        $cartPageTotal = max(0, $discountableAmount - $discount);
        $checkoutTotal = $cartPageTotal + $shippingCost;
        
        $cart->update([
            'subtotal' => $cartTotal,
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

        $discount = $cart->discount_amount ?? 0;
        $shippingCost = $cart->shipping_cost ?? 0;
        
        // Cart page total = items total + installation fees
        $cartPageTotal = $cartTotal + $installationFee;
        $discountableAmount = $cartPageTotal;
        $cartPageTotalAfterDiscount = max(0, $discountableAmount - $discount);
        
        // Checkout total = cart total + shipping
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
        
        $cart->subtotal = $subtotal;
        $cart->total = $checkoutTotal;
        $cart->save();

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
                    
                    $itemData = [
                        'type' => class_basename($item->buyable_type),
                        'id' => $buyable->id,
                        'name' => $this->resolveName($buyable),
                        'price_per_unit' => (float) $item->price_per_unit, // Including VAT
                        'quantity' => $item->quantity,
                        'subtotal' => (float) ($paidQuantity * $item->price_per_unit), // Use paid quantity × price including VAT
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
                        'response_subtotal' => $itemData['subtotal']
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
        $days = $request->input('days', 7);

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
        // Handle different product types with their specific image methods
        if ($buyable instanceof \App\Models\Tyre) {
            return $buyable->tyre_feature_image_url ?? $buyable->tyre_secondary_image_url;
        }
        
        if ($buyable instanceof \App\Models\Battery) {
            return $buyable->feature_image_url ?? $buyable->image_url;
        }
        
        if ($buyable instanceof \App\Models\AutoPart) {
            $url = $buyable->getAutoPartSecondaryImageUrl();
            if (!empty($url)) return $url;
            
            if (!empty($buyable->photo_link)) {
                // Check if photo_link is already a full URL
                if (str_starts_with($buyable->photo_link, 'http')) {
                    return $buyable->photo_link;
                }
                return asset('storage/' . $buyable->photo_link);
            }
        }
        
        if ($buyable instanceof \App\Models\Rim) {
            if (method_exists($buyable, 'getRimFeatureImageUrl')) {
                $url = $buyable->getRimFeatureImageUrl();
                if (!empty($url)) return $url;
            }
        }

        // Fallback to media library collections
        if ($buyable instanceof \Spatie\MediaLibrary\HasMedia) {
            $collections = ['feature', 'tyre_feature_image', 'battery_feature_image', 'auto_part_secondary_image', 'rim_feature_image'];
            
            foreach ($collections as $collection) {
                $url = $buyable->getFirstMediaUrl($collection);
                if (!empty($url)) return $url;
            }
            
            // Try any media as last resort
            $url = $buyable->getFirstMediaUrl();
            if (!empty($url)) return $url;
        }

        return null;
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
        if ($request->has('cart_payload')) {
            $request->merge([
                'items' => $request->input('cart_payload')
            ]);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.buyable_id' => 'required|integer|exists:tyres,id',
            'items.*.quantity' => 'nullable|integer|min:1'
        ]);

        $cart = \App\Services\CartService::getCurrentCart();

        foreach ($request->items as $itemData) {
            $tyre = \App\Models\Tyre::find($itemData['buyable_id']);
            $quantity = $itemData['quantity'] ?? 1;
            $price = $tyre->discounted_price ?? $tyre->price_vat_inclusive ?? $tyre->regular_price ?? 0;
            
            // Calculate paid quantity for buy 3 get 1 free
            $isOfferActive = $tyre->buy_3_get_1_free ?? false;
            $paidQuantity = $isOfferActive && $quantity >= 3 
                ? $quantity - floor($quantity / 4) 
                : $quantity;

            $cart->items()->create([
                'buyable_type'   => $tyre->getMorphClass(), 
                'buyable_id'     => $tyre->id,
                'quantity'       => $quantity,
                'price_per_unit' => $price,
                'subtotal'       => $price * $paidQuantity,
            ]);
        }

        return response()->json([
            'status'     => 'success',
            'message'    => 'Tyre group added to cart successfully.',
            'session_id' => session()->getId(),
            'cart'       => $cart->load('items')
        ]);
    }



}

