<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\JeeblyService;
use App\Services\ShippingCalculatorService;
use App\Services\PaymobPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     * User Checkout (Auth only)
     */


    public function userCheckout(Request $request)
    {
        try {
            // \Log::info('Checkout Step 1: Starting userCheckout');
            $itemsData = $request->input('items', []);
            \Log::info('Checkout Step 2: Got items data', ['items_count' => count($itemsData)]);
            $validationErrors = [];

        foreach ($itemsData as $index => $item) {
            $shippingOption = $item['shipping_option'] ?? 'delivery_only';

            if ($shippingOption === 'with_installation') {
                if (empty($item['mobile_van_id'])) {
                    $validationErrors["items.$index.mobile_van_id"] = 'Mobile van ID is required for installation at home.';
                }
                if (empty($item['installation_date'])) {
                    $validationErrors["items.$index.installation_date"] = 'Installation date is required for home service.';
                }
            }

            if ($shippingOption === 'installation_center') {
                if (empty($item['installation_center_id'])) {
                    $validationErrors["items.$index.installation_center_id"] = 'Installation center ID is required.';
                }
                if (empty($item['installation_date'])) {
                    $validationErrors["items.$index.installation_date"] = 'Installation date is required for installation center.';
                }
            }
        }

        if (!empty($validationErrors)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validationErrors,
            ], 422);
        }

        // \Log::info('Checkout Step 3: Getting user');
        $user = Auth::user();
        if (!$user) {
            // \Log::error('Checkout Error: User not authenticated');
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }
        \Log::info('Checkout Step 4: User found', ['user_id' => $user->id]);
        
        $paymentMethod = $request->input('payment_method', 'paymob');
        if ($paymentMethod !== 'paymob') {
            return response()->json(['message' => 'Only Paymob payment is supported.'], 400);
        }
        // \Log::info('Checkout Step 5: Payment method validated');

        \Log::info('Checkout Step 6: Getting cart for user', ['user_id' => $user->id]);
        $cart = Cart::with('items.buyable')->where('user_id', $user->id)->first();
        if (!$cart) {
            \Log::error('Checkout Error: No cart found', ['user_id' => $user->id]);
            return response()->json(['status' => 'error', 'message' => 'No cart found for user'], 400);
        }
        \Log::info('Checkout Step 7: Cart found', ['cart_id' => $cart->id, 'items_count' => $cart->items->count()]);
        
        // \Log::info('Checkout Step 8: Handling address selection');
        // Handle address selection or manual entry
        $selectedAddress = null;
        $useManualAddress = false;
        
        if ($request->has('address_id') && $request->address_id) {
            \Log::info('Checkout Step 9: Looking for address', ['address_id' => $request->address_id]);
            $selectedAddress = UserAddress::where('user_id', $user->id)->find($request->address_id);
        }
        
        if (!$selectedAddress) {
            // \Log::info('Checkout Step 10: Looking for primary address');
            $selectedAddress = UserAddress::where('user_id', $user->id)->where('is_primary', true)->first();
        }
        
        // If no saved address, check if manual address data is provided
        if (!$selectedAddress) {
            // Log all request data for debugging
            \Log::info('Checkout: No saved address, checking manual input', [
                'all_request_keys' => array_keys($request->all()),
                'all_request_data' => $request->all()
            ]);
            
            // Accept various field name formats - be very flexible
            $areaIdValue = $request->input('area_id') 
                ?? $request->input('area') 
                ?? $request->input('city_id')
                ?? $request->input('governorate_id');
            
            $addressLineValue = $request->input('address_line') 
                ?? $request->input('address') 
                ?? $request->input('address_line_1')
                ?? $request->input('address_name')
                ?? $request->input('street_address');
            
            $phoneValue = $request->input('phone') 
                ?? $request->input('mobile')  // Frontend sends this!
                ?? $request->input('mobile_number') 
                ?? $request->input('contact_phone')
                ?? $request->input('phone_number')
                ?? $user->phone;
            
            \Log::info('Checkout: Extracted address values', [
                'area_id' => $areaIdValue,
                'address_line' => $addressLineValue,
                'phone' => $phoneValue
            ]);
            
            if ($areaIdValue && $addressLineValue && $phoneValue) {
                \Log::info('Checkout Step 10b: Using manual address data', [
                    'area_id' => $areaIdValue,
                    'address_line' => $addressLineValue,
                    'phone' => $phoneValue
                ]);
                $useManualAddress = true;
                $areaId = $areaIdValue;
            } else {
                \Log::error('Checkout Error: No address found and no manual address provided', [
                    'has_area_id' => !empty($areaIdValue),
                    'has_address_line' => !empty($addressLineValue),
                    'has_phone' => !empty($phoneValue),
                    'all_fields' => $request->all()
                ]);
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Please provide address details (area_id, address_line, phone)',
                    'debug' => [
                        'received_fields' => array_keys($request->all()),
                        'missing' => [
                            'area' => empty($areaIdValue) ? 'missing' : 'found',
                            'address' => empty($addressLineValue) ? 'missing' : 'found',
                            'phone' => empty($phoneValue) ? 'missing' : 'found',
                        ]
                    ]
                ], 422);
            }
        } else {
            \Log::info('Checkout Step 11: Address found', ['address_id' => $selectedAddress->id]);
            $areaId = $selectedAddress->area_id ?? $request->input('area_id') ?? $request->input('area');
        }
        
        if (!$areaId) {
            \Log::error('Checkout Error: No area ID', $selectedAddress ? ['selected_address' => $selectedAddress->toArray()] : ['manual_address' => true]);
            return response()->json(['status' => 'error', 'message' => 'No valid area found.'], 422);
        }
        \Log::info('Checkout Step 12: Area ID found', ['area_id' => $areaId]);

        // \Log::info('Checkout Step 13: Checking cart items');
        if ($cart->items->isEmpty()) {
            // \Log::error('Checkout Error: Cart is empty');
            return response()->json(['status' => 'error', 'message' => 'Cart is empty.'], 400);
        }
        \Log::info('Checkout Step 14: Cart has items', ['items_count' => $cart->items->count()]);

        \Log::info('Checkout Step 15: Updating cart area', ['area_id' => $areaId]);
        $cart->update(['area_id' => $areaId]);
        
        // Use cart summary for all calculations
        $cartSummary = CartService::generateCartSummary($cart);
        $shipping = [
            'breakdown' => $cartSummary['shipping_breakdown'],
            'total' => $cartSummary['totals']['shipping']
        ];

        $installationGroups = [];
        foreach ($cart->items as $cartItem) {
            if ($cartItem->shipping_option === 'with_installation') {
                $key = $cartItem->mobile_van_id . '_' . $cartItem->installation_date;
                $installationGroups[$key] = true;
            }
        }
        $settings = \App\Models\ShippingSetting::first();
        $installationFeeValue = $settings?->installation_fee ?? 200;
        $installationFees = count($installationGroups) * $installationFeeValue;


        // Calculate subtotal same as cart summary (VAT-exclusive)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $subtotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $priceWithoutVat = $item->price_per_unit - ($item->price_per_unit * $vatPercent);
            $subtotal += $paidQuantity * $priceWithoutVat;
        }

        // Coupon logic
        $coupon = null;
        $discountAmount = 0;
        $couponCode = $request->input('coupon_code') ?? $cart->applied_coupon;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->first();

            if (!$coupon) return response()->json(['message' => 'Invalid or expired coupon code.'], 400);

            $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('order_id')->count();
            $usedByUser = CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('order_id')->where('user_id', $user->id)->count();

            if ($coupon->usage_limit && $totalUsed >= $coupon->usage_limit) return response()->json(['message' => 'Coupon usage limit reached.'], 400);
            if ($coupon->usage_limit_per_user && $usedByUser >= $coupon->usage_limit_per_user) return response()->json(['message' => 'You have already used this coupon.'], 400);

            if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) return response()->json(['message' => 'Order total below coupon minimum.'], 400);

            switch ($coupon->type) {
                case 'free_shipping':
                    $shipping['total'] = 0;
                    break;
                case 'discount_amount':
                    $discountAmount = min($coupon->value, $subtotal);
                    break;
                case 'discount_percentage':
                    $discountAmount = round($subtotal * ($coupon->value / 100), 2);
                    break;
            }
        } else {
            $discountAmount = $cart->discount_amount ?? 0;
        }

        // Calculate VAT exactly same as cart summary
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 ? $item->quantity - 1 : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit; // Including VAT
        }
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 ? $item->quantity - 1 : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit;
        }
        $menuSubtotal = $menuTotal - ($menuTotal * $vatPercent);
        $vatAmount = $cartSummary['totals']['vat'];
        $total = $cartSummary['totals']['total'];

        // comment

        // Get location IDs from selected address or manual area input
        $area = $useManualAddress ? \App\Models\Area::with('city')->find($request->input('area_id')) : null;
        $countryId = $selectedAddress?->country_id ?? $request->input('country_id') ?? null;
        $governorateId = $selectedAddress?->governorate_id ?? $request->input('governorate_id') ?? null;
        $cityId = $selectedAddress?->city_id ?? $area?->city_id ?? $request->input('city_id') ?? null;
        
        $order = Order::create([
            'user_id'           => $user->id,
            'coupon_id'         => $coupon?->id,
            'subtotal'          => round($cartSummary['totals']['items_subtotal'], 2),
            'shipping_cost'     => round($cartSummary['totals']['shipping'], 2),
            'installation_fees' => round($cartSummary['totals']['installation'], 2),
            'tax_amount'        => round($cartSummary['totals']['vat'], 2),
            'discount'          => round($cartSummary['totals']['discount'], 2),
            'coupon_code'       => $coupon?->code,
            'shipping_breakdown'=> $shipping['breakdown'],
            'shipping_response' => json_encode($shipping['breakdown']),
            'total'             => round($total, 2),
            'car_make'          => $request->input('car_make'),
            'car_model'         => $request->input('car_model'),
            'car_year'          => $request->input('car_year'),
            'plate_number'      => $request->input('plate_number'),
            'vin'               => $request->input('vin'),
            'payment_method'    => 'paymob',
            'country_id'        => $countryId,
            'governorate_id'    => $governorateId,
            'city_id'           => $cityId,
            'title'             => $request->input('title'),
        ]);

        // great point

        foreach ($cart->items as $cartItem) {
            $product = $cartItem->buyable;
            if (!$product) {
                \Log::error('Product not found for cart item', ['cart_item_id' => $cartItem->id]);
                continue;
            }
            $unitPrice = $cartItem->price_per_unit;
            $itemData = collect($itemsData)->first(fn($i) => $i['buyable_type'] === strtolower(class_basename($cartItem->buyable_type)) && $i['buyable_id'] == $product->id);

            OrderItem::create([
                'order_id' => $order->id,
                'buyable_type' => get_class($product),
                'buyable_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price_per_unit' => $unitPrice,
                'subtotal' => $cartItem->subtotal,
                'product_name' => $product->name ?? '',
                'sku' => $product->sku ?? '',
                'image_url' => $product->getFirstMediaUrl('feature_image') ?? '',
                'shipping_option' => $cartItem->shipping_option ?? 'delivery_only',
                'installation_center_id' => $cartItem->installation_center_id,
                'mobile_van_id' => $cartItem->mobile_van_id,
                'installation_date' => $cartItem->installation_date,
            ]);
        }

        // Create OrderAddress from saved address or manual data
        if ($selectedAddress) {
            OrderAddress::create([
                'order_id'       => $order->id,
                'type'           => 'shipping',
                'country_id'     => $selectedAddress->country_id,
                'governorate_id' => $selectedAddress->governorate_id,
                'city_id'        => $selectedAddress->city_id,
                'area_id'        => $selectedAddress->area_id,
                'address_line'   => $selectedAddress->address_line_1 ?? $selectedAddress->address_line,
                'phone'          => $selectedAddress->phone,
                'notes'          => $selectedAddress->additional_info ?? null,
            ]);
        } elseif ($useManualAddress) {
            // Create order address from manual input (using flexible field names)
            $areaIdValue = $request->input('area_id') ?? $request->input('area');
            $addressLineValue = $request->input('address_line') ?? $request->input('address') ?? $request->input('address_line_1');
            $phoneValue = $request->input('phone') ?? $request->input('mobile') ?? $request->input('mobile_number') ?? $request->input('contact_phone');
            
            OrderAddress::create([
                'order_id'       => $order->id,
                'type'           => 'shipping',
                'country_id'     => $request->input('country_id') ?? null,
                'governorate_id' => $request->input('governorate_id') ?? null,
                'city_id'        => $request->input('city_id') ?? $area?->city_id ?? null,
                'area_id'        => $areaIdValue,
                'address_line'   => $addressLineValue,
                'phone'          => $phoneValue,
                'notes'          => $request->input('notes') ?? $request->input('additional_instructions') ?? null,
            ]);
        }

        try {
            (new JeeblyService())->createShipment($order);
        } catch (\Exception $e) {
            \Log::error('Failed to create shipment with Jeebly', ['error' => $e->getMessage()]);
        }

        if ($coupon) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id'   => $user->id,
                'session_id'=> session()->getId(),
                'order_id'  => $order->id,
            ]);
        }

        $paymob = new PaymobPaymentService();
        $phoneForPayment = $selectedAddress?->phone 
            ?? $request->input('phone') 
            ?? $request->input('mobile')
            ?? $request->input('mobile_number') 
            ?? $request->input('contact_phone')
            ?? $user->phone 
            ?? '01000000000';
            
        $request->merge([
            'amount_cents'      => intval(round($order->total * 100)),
            'contact_email'     => $user->email,
            'name'              => $user->name,
            'merchant_order_id' => $order->id,
            'phone_number'      => $phoneForPayment,
        ]);

        $iframeResult = $paymob->sendPayment($request);
        $order->update([
            'payment_url'     => $iframeResult['iframe_url'] ?? null,
            'paymob_order_id' => $iframeResult['paymob_order_id'] ?? null,
        ]);

        $cart->delete();
        // Email will be sent after payment confirmation in callback

        // \Log::info('Checkout Step 19: Checkout completed successfully');
        return response()->json([
            'order_id'           => $order->id,
            'paymob_order_id'    => $order->paymob_order_id,
            'total'              => $order->total,
            'discount'           => $discountAmount,
            'coupon_code'        => $coupon?->code,
            'shipping_cost'      => $shipping['total'],
            'installation_fees'  => $installationFees,
            'shipping_breakdown' => $shipping['breakdown'],
            'payment_method'     => 'paymob',
            'payment_url'        => $iframeResult['iframe_url'] ?? null,
        ]);
        
        } catch (\Exception $e) {
            \Log::error('Checkout Fatal Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Checkout failed: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }




    /**
     * Guest Checkout (session-based)
     */
    public function guestCheckout(Request $request)
    {
        try {
        $sessionId = $request->header('X-Session-ID') ?? session()->getId();

        $validated = $request->validate([
            'title' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'address_id' => 'nullable|integer|exists:user_addresses,id',
            'address_name' => 'nullable|string',
            'country_id' => 'required_without:address_id|integer',
            'governorate_id' => 'required_without:address_id|integer',
            'city_id' => 'required_without:address_id|integer',
            'area_id' => 'required_without:address_id|integer',
            'address_line' => 'required_without:address_id|string',
            'additional_instructions' => 'nullable|string',

            // Vehicle
            'car_make' => 'nullable|string',
            'car_model' => 'nullable|string',
            'car_year' => 'nullable|integer',
            'plate_number' => 'nullable|string',
            'vin' => 'nullable|string',

            // Items shipping info
            'items' => 'required|array|min:1',
            'items.*.buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'items.*.buyable_id' => 'required|integer',
            'items.*.shipping_option' => 'nullable|string|in:delivery_only,with_installation,installation_center',
            'items.*.installation_center_id' => 'nullable|integer',
            'items.*.mobile_van_id' => 'nullable|integer',
            'items.*.installation_date' => 'nullable|date',
            'coupon_code' => 'nullable|string',
        ]);

        $cart = Cart::with('items.buyable')->where('session_id', $sessionId)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        // Handle address selection vs manual entry
        if (!empty($validated['address_id'])) {
            $selectedAddress = UserAddress::find($validated['address_id']);
            if (!$selectedAddress) {
                return response()->json(['message' => 'Selected address not found.'], 400);
            }
            
            // Use selected address data
            $areaId = $selectedAddress->area_id;
            $validated['country_id'] = $selectedAddress->country_id;
            $validated['governorate_id'] = $selectedAddress->governorate_id;
            $validated['city_id'] = $selectedAddress->city_id;
            $validated['address_line'] = $selectedAddress->address_line_1;
            $validated['additional_instructions'] = $selectedAddress->additional_info;
        } else {
            $areaId = $validated['area_id'];
        }
        $cart->update(['area_id' => $areaId]);

        $shipping = ShippingCalculatorService::calculate($cart, 5); // use default 5 shipments for guests
        if (!empty($shipping['error'])) {
            return response()->json(['message' => 'Shipping calculation failed.'], 400);
        }

        //  Installation Fees Calculation (Updated)
        $installationGroups = [];
        foreach ($cart->items as $cartItem) {
            if ($cartItem->shipping_option === 'with_installation') {
                $key = $cartItem->mobile_van_id . '_' . $cartItem->installation_date;
                $installationGroups[$key] = true;
            }
        }
        $settings = \App\Models\ShippingSetting::first();
        $installationFeeValue = $settings?->installation_fee ?? 200;
        $installationFees = count($installationGroups) * $installationFeeValue;


        // Calculate subtotal same as cart summary (VAT-exclusive)
        $vatPercent = \App\Models\ShippingSetting::first()?->vat_percent ?? 0.05;
        $subtotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 
                ? $item->quantity - 1 
                : $item->quantity;
            $priceWithoutVat = $item->price_per_unit - ($item->price_per_unit * $vatPercent);
            $subtotal += $paidQuantity * $priceWithoutVat;
        }

        // Coupon logic
        $coupon = null;
        $discountAmount = 0;
        $couponCode = $validated['coupon_code'] ?? $cart->applied_coupon;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->first();

            if (!$coupon) return response()->json(['message' => 'Invalid or expired coupon code.'], 400);

            $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('order_id')->count();
            $usedByGuest = CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('order_id')->where('session_id', $sessionId)->count();

            if ($coupon->usage_limit && $totalUsed >= $coupon->usage_limit) return response()->json(['message' => 'Coupon usage limit reached.'], 400);
            if ($coupon->usage_limit_per_user && $usedByGuest >= $coupon->usage_limit_per_user) return response()->json(['message' => 'You have already used this coupon.'], 400);

            if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) return response()->json(['message' => 'Order total below coupon minimum.'], 400);

            switch ($coupon->type) {
                case 'free_shipping':
                    $shipping['total'] = 0;
                    break;
                case 'discount_amount':
                    $discountAmount = min($coupon->value, $subtotal);
                    break;
                case 'discount_percentage':
                    $discountAmount = round($subtotal * ($coupon->value / 100), 2);
                    break;
            }
        } else {
            $discountAmount = $cart->discount_amount ?? 0;
        }

        // Calculate VAT exactly same as cart summary
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 ? $item->quantity - 1 : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit; // Including VAT
        }
        $menuTotal = 0;
        foreach ($cart->items as $item) {
            if (!$item->buyable) continue;
            $isOfferActive = ($item->buyable instanceof \App\Models\Tyre) && ($item->buyable->buy_3_get_1_free ?? false);
            $paidQuantity = $isOfferActive && $item->quantity >= 3 ? $item->quantity - 1 : $item->quantity;
            $menuTotal += $paidQuantity * $item->price_per_unit;
        }
        $menuSubtotal = $menuTotal - ($menuTotal * $vatPercent);
        // Use cart summary total to ensure exact match
        $cartSummary = CartService::generateCartSummary($cart);
        $vatAmount = $cartSummary['totals']['vat'];
        $total = $cartSummary['totals']['total'];


        $order = Order::create([
            'user_id' => null,
            'coupon_id' => $coupon?->id,
            'contact_name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'contact_email' => $validated['email'],
            'contact_phone' => $validated['mobile'],
            'subtotal' => round($cartSummary['totals']['items_subtotal'], 2),
            'shipping_cost' => round($cartSummary['totals']['shipping'], 2),
            'installation_fees' => round($cartSummary['totals']['installation'], 2),
            'tax_amount' => round($cartSummary['totals']['vat'], 2),
            'discount' => round($cartSummary['totals']['discount'], 2),
            'coupon_code' => $coupon?->code,
            'shipping_breakdown' => $shipping['breakdown'],
            'shipping_response' => json_encode($shipping['breakdown']),
            'total' => round($total, 2),
            'payment_method' => 'paymob',
            'country_id' => $validated['country_id'],
            'governorate_id' => $validated['governorate_id'],
            'city_id' => $validated['city_id'],
            'checkout_token' => $sessionId . '_' . time() . '_' . rand(1000, 9999),
            'car_make' => $validated['car_make'] ?? null,
            'car_model' => $validated['car_model'] ?? null,
            'car_year' => $validated['car_year'] ?? null,
            'plate_number' => $validated['plate_number'] ?? null,
            'vin' => $validated['vin'] ?? null,
            'title' => $validated['title'] ?? null,
        ]);

        foreach ($cart->items as $cartItem) {
            $product = $cartItem->buyable;
            if (!$product) {
                \Log::error('Product not found for cart item', ['cart_item_id' => $cartItem->id]);
                continue;
            }
            $itemData = collect($validated['items'])->first(fn($i) => $i['buyable_type'] === strtolower(class_basename($cartItem->buyable_type)) && $i['buyable_id'] == $product->id);

            OrderItem::create([
                'order_id' => $order->id,
                'buyable_type' => get_class($product),
                'buyable_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price_per_unit' => $cartItem->price_per_unit,
                'subtotal' => $cartItem->price_per_unit * $cartItem->quantity,
                'product_name' => $product->name ?? '',
                'sku' => $product->sku ?? '',
                'image_url' => $product->getFirstMediaUrl('feature_image') ?? '',
                'shipping_option' => $cartItem->shipping_option ?? 'delivery_only',
                'installation_center_id' => $cartItem->installation_center_id,
                'mobile_van_id' => $cartItem->mobile_van_id,
                'installation_date' => $cartItem->installation_date,
            ]);
        }

        // Save Guest Address
        OrderAddress::create([
            'order_id' => $order->id,
            'type' => 'shipping',
            'country_id' => $validated['country_id'],
            'governorate_id' => $validated['governorate_id'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'address_line' => $validated['address_line'],
            'phone' => $validated['mobile'],
        ]);

        try {
            (new JeeblyService())->createShipment($order);
        } catch (\Exception $e) {
            \Log::error('Failed to create shipment with Jeebly', ['error' => $e->getMessage()]);
        }

        if ($coupon) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'session_id'=> $sessionId,
                'order_id'  => $order->id,
            ]);
        }

        // Paymob
        $request->merge([
            'amount_cents' => intval(round($order->total * 100)),
            'contact_email' => $validated['email'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'merchant_order_id' => $order->id,
            'phone_number' => $validated['mobile'],
        ]);

        $paymob = new PaymobPaymentService();
        $iframe = $paymob->sendPayment($request);

        $order->update([
            'payment_url' => $iframe['iframe_url'] ?? null,
            'paymob_order_id' => $iframe['paymob_order_id'] ?? null,
        ]);

        $cart->delete();
        // Email will be sent after payment confirmation in callback

        return response()->json([
            'order_id' => $order->id,
            'paymob_order_id' => $order->paymob_order_id,
            'total' => $order->total,
            'discount' => $discountAmount,
            'coupon_code' => $coupon?->code,
            'installation_fees'  => $installationFees,
            'shipping_breakdown' => $shipping['breakdown'],
            'shipping_cost' => $shipping['total'],
            'payment_url' => $iframe['iframe_url'] ?? null,
        ]);
        } catch (\Exception $e) {
            \Log::error('Guest Checkout Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTracking($id)
    {
        $order = Order::findOrFail($id);

        if (!Auth::check() || Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$order->tracking_number) {
            return response()->json(['message' => 'No tracking number found for this order.'], 404);
        }

        $trackingData = (new JeeblyService())->trackShipment($order->tracking_number);
        $tracking = $trackingData['Tracking'] ?? [];

        return response()->json([
            'order_id'        => $order->id,
            'tracking_number' => $order->tracking_number,
            'tracking_url'    => $order->tracking_url,
            'status'          => $tracking['last_status'] ?? null,
            'desc'            => $tracking['description'] ?? null,
            'hub_name'        => $tracking['hub_name'] ?? null,
            'event_date_time' => $tracking['event_date_time'] ?? null,
            'failure_reason'  => $tracking['failure_reason'] ?? null,
            'timeline'        => $tracking['events'] ?? [],

        ]);
    }


    public function getGuestTracking($id, Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? session()->getId();
        $order = Order::where('id', $id)
            ->where('checkout_token', $sessionId)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$order->tracking_number) {
            return response()->json(['message' => 'No tracking number found for this order.'], 404);
        }

        $trackingData = (new JeeblyService())->trackShipment($order->tracking_number);
        $tracking = $trackingData['Tracking'] ?? [];

        return response()->json([
            'order_id'        => $order->id,
            'tracking_number' => $order->tracking_number,
            'tracking_url'    => $order->tracking_url,
            'status'          => $tracking['last_status'] ?? null,
            'desc'            => $tracking['description'] ?? null,
            'hub_name'        => $tracking['hub_name'] ?? null,
            'event_date_time' => $tracking['event_date_time'] ?? null,
            'failure_reason'  => $tracking['failure_reason'] ?? null,
            'timeline'        => $tracking['shipment_timeline'] ?? [],
        ]);
    }


    public function updateCartArea(Request $request)
    {
        $request->validate([
            'area_id' => 'required|integer|exists:areas,id'
        ]);
        
        $cart = CartService::getCurrentCart();
        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }
        
        // Update area cost: subtract old area cost, add new area cost
        $hasDeliveryOnly = $cart->items->contains('shipping_option', 'delivery_only');
        if ($hasDeliveryOnly) {
            $currentShippingCost = $cart->shipping_cost ?? 0;
            
            // Subtract previous area cost if exists
            if ($cart->area_id) {
                $previousArea = \App\Models\Area::find($cart->area_id);
                $previousAreaCost = $previousArea?->shipping_cost ?? 0;
                $currentShippingCost -= $previousAreaCost;
            }
            
            // Add new area cost
            $area = \App\Models\Area::find($request->area_id);
            $areaShippingCost = $area?->shipping_cost ?? 0;
            $totalShippingCost = $currentShippingCost + $areaShippingCost;
            
            $cart->update([
                'area_id' => $request->area_id,
                'shipping_cost' => $totalShippingCost
            ]);
        } else {
            $cart->update(['area_id' => $request->area_id]);
        }
        
        // Return updated cart summary with new shipping cost
        $summary = CartService::generateCartSummary($cart);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cart area updated successfully',
            'summary' => $summary
        ]);
    }

    public function getUserOrders(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = Order::with('items')->where('user_id', $user->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('tracking_number', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'order_id'      => $order->id,
                    'date'          => $order->created_at->format('F j, Y'),
                    'status'        => $order->status ?? 'pending',
                    'total'         => 'AED ' . number_format($order->total, 2) . ' for ' . $order->items->count() . ' item' . ($order->items->count() > 1 ? 's' : ''),
                    'tracking_no'   => $order->tracking_number,
                    'view_url'      => route('api.orders.view', ['id' => $order->id]),
                ];
            });

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function getUserOrdersByStatus(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');

        $query = Order::with('items')->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'order_id'      => $order->id,
                    'date'          => $order->created_at->format('F j, Y'),
                    'status'        => $order->status ?? 'pending',
                    'total'         => 'AED ' . number_format($order->total, 2) . ' for ' . $order->items->count() . ' item' . ($order->items->count() > 1 ? 's' : ''),
                    'tracking_no'   => $order->tracking_number,
                    'view_url'      => route('api.orders.view', ['id' => $order->id]),
                ];
            });

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function getOrderStatuses()
    {
        $statuses = [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'paid', 'label' => 'Paid'],
            ['value' => 'preparing', 'label' => 'Preparing'],
            ['value' => 'shipping', 'label' => 'Shipping'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'delayed', 'label' => 'Delayed'],
            ['value' => 'cancelled', 'label' => 'Cancelled'],
            ['value' => 'refund', 'label' => 'Refund'],
            ['value' => 'payment_failed', 'label' => 'Payment Failed'],
        ];
        
        // Sort by label alphabetically
        usort($statuses, function($a, $b) {
            return strcmp($a['label'], $b['label']);
        });
        
        return response()->json([
            'statuses' => $statuses
        ]);
    }
    
    public function getCheckoutAddresses(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required',
                'is_authenticated' => false,
                'has_addresses' => false,
                'addresses' => [],
            ], 401);
        }
        
        $addresses = UserAddress::with(['country', 'governorate', 'city', 'area'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->label,
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'address_line' => $address->address_line_1,
                    'additional_info' => $address->additional_info,
                    'country_id' => $address->country_id,
                    'governorate_id' => $address->governorate_id,
                    'city_id' => $address->city_id,
                    'area_id' => $address->area_id,
                    'country_name' => $address->country?->name,
                    'governorate_name' => $address->governorate?->name,
                    'city_name' => $address->city?->name,
                    'area_name' => $address->area?->name,
                    'is_primary' => $address->is_primary,
                    'display_text' => $address->label . ' - ' . $address->address_line_1 . ', ' . $address->area?->name . ', ' . $address->city?->name,
                ];
            });
            
        return response()->json([
            'status' => 'success',
            'is_authenticated' => true,
            'has_addresses' => $addresses->count() > 0,
            'addresses' => $addresses,
        ]);
    }
    
    public function saveCheckoutAddress(Request $request)
    {
        $validated = $request->validate([
            'address_name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'area_id' => 'required|integer|exists:areas,id',
        ]);
        
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required to save address'
            ], 401);
        }
        
        // Check if user already has addresses
        $existingAddresses = UserAddress::where('user_id', $user->id)->count();
        
        // If no existing addresses, make this primary
        if ($existingAddresses === 0) {
            $validated['is_primary'] = true;
        }
        
        // If setting as primary, remove primary from other addresses
        if ($validated['is_primary'] ?? false) {
            UserAddress::where('user_id', $user->id)->update(['is_primary' => false]);
        }
        
        $city = \App\Models\City::find($validated['city_id']);
        
        $address = UserAddress::create([
            'user_id' => $user->id,
            'session_id' => null,
            'label' => $validated['address_name'],
            'phone' => $validated['phone'],
            'address_line_1' => $validated['address'],
            'country_id' => $validated['country_id'],
            'governorate_id' => $city->governorate_id,
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'is_primary' => $validated['is_primary'] ?? false,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Address saved successfully',
            'address' => [
                'id' => $address->id,
                'address_name' => $address->label,
                'display_text' => $address->label . ' - ' . $address->address_line_1,
            ],
        ]);
    }

    public function getOrderDetails($id)
    {
        $order = Order::with([
            'items.buyable', 
            'items.mobileVan',
            'items.installationCenter',
            'shippingAddress.area', 
            'shippingAddress.city', 
            'user',
            'coupon'
        ])->findOrFail($id);

        // Check if user owns this order
        if (auth()->id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Format order data with complete details
        $orderData = [
            'order' => [
                'id' => $order->id,
                'created_at' => $order->created_at->format('d M Y, h:i A'),
                'created_at_timestamp' => $order->created_at->timestamp,
                'status' => $order->status,
                'payment_method' => ucfirst($order->payment_method ?? 'N/A'),
                'tracking_number' => $order->tracking_number,
                'tracking_url' => $order->tracking_url,
                'shipping_company' => $order->shipping_company,
            ],
            'customer' => [
                'name' => $order->user->name ?? $order->contact_name ?? 'Guest',
                'email' => $order->user->email ?? $order->contact_email,
                'phone' => $order->user->phone ?? $order->contact_phone,
            ],
            'vehicle' => [
                'car_make' => $order->car_make,
                'car_model' => $order->car_model,
                'car_year' => $order->car_year,
                'plate_number' => $order->plate_number,
                'vin' => $order->vin,
            ],
            'shipping_address' => $order->shippingAddress ? [
                'id' => $order->shippingAddress->id,
                'address_line' => $order->shippingAddress->address_line,
                'area' => $order->shippingAddress->area->name ?? '-',
                'city' => $order->shippingAddress->city->name ?? '-',
                'phone' => $order->shippingAddress->phone,
                'notes' => $order->shippingAddress->notes,
                'latitude' => $order->shippingAddress->latitude,
                'longitude' => $order->shippingAddress->longitude,
            ] : null,
            'items' => $order->items->map(function ($item) {
                $product = $item->buyable;
                
                // Resolve product name (same logic as CartController and order-items-table)
                $productName = $product->name 
                    ?? ($product->product_name ?? null)
                    ?? ($product->title ?? '')
                    ?? $item->product_name
                    ?? '-';
                
                // Get product image
                $productImage = $this->resolveImage($product);
                
                // Get shipping option details
                $shippingOption = $item->shipping_option ?: 'delivery_only';
                $shippingLabel = match ($shippingOption) {
                    'delivery_only' => 'Delivery Only',
                    'with_installation' => 'Delivery + Home Installation',
                    'installation_center' => 'Installation Center (Pick-up)',
                    default => 'Unknown',
                };
                
                $itemData = [
                    'id' => $item->id,
                    'type' => class_basename($item->buyable_type),
                    'product_id' => $item->buyable_id,
                    'product_name' => $productName,
                    'quantity' => $item->quantity,
                    'price_per_unit' => (float) $item->price_per_unit,
                    'price_per_unit_formatted' => number_format($item->price_per_unit, 2) . ' AED',
                    'subtotal' => (float) $item->subtotal,
                    'subtotal_formatted' => number_format($item->subtotal, 2) . ' AED',
                    'image' => $productImage,
                    'shipping_option' => $shippingOption,
                    'shipping_option_label' => $shippingLabel,
                ];
                
                // Add Buy 3 Get 1 info for tires
                if ($product && isset($product->buy_3_get_1_free)) {
                    $itemData['buy_3_get_1_free'] = $product->buy_3_get_1_free;
                }
                
                // Add mobile van details if applicable
                if ($shippingOption === 'with_installation' && $item->mobileVan) {
                    $itemData['mobile_van'] = [
                        'id' => $item->mobileVan->id,
                        'name' => $item->mobileVan->name,
                        'location' => $item->mobileVan->location,
                        'phone' => $item->mobileVan->phone ?? null,
                    ];
                }
                
                // Add installation center details if applicable
                if ($shippingOption === 'installation_center' && $item->installationCenter) {
                    $itemData['installation_center'] = [
                        'id' => $item->installationCenter->id,
                        'name' => $item->installationCenter->name,
                        'location' => $item->installationCenter->location,
                        'phone' => $item->installationCenter->phone ?? null,
                        'city' => $item->installationCenter->city ?? null,
                        'google_maps_link' => $item->installationCenter->google_maps_link ?? null,
                    ];
                }
                
                // Add installation date if scheduled
                if ($item->installation_date) {
                    $itemData['installation_date'] = $item->installation_date;
                    $itemData['installation_date_formatted'] = \Carbon\Carbon::parse($item->installation_date)->format('d M Y, h:i A');
                }
                
                return $itemData;
            }),
            'cost_breakdown' => [
                'subtotal' => (float) $order->subtotal,
                'subtotal_formatted' => number_format($order->subtotal, 2) . ' AED',
                'shipping_cost' => (float) $order->shipping_cost,
                'shipping_cost_formatted' => number_format($order->shipping_cost, 2) . ' AED',
                'installation_fees' => (float) ($order->installation_fees ?? 0),
                'installation_fees_formatted' => number_format($order->installation_fees ?? 0, 2) . ' AED',
                'tax_amount' => (float) $order->tax_amount,
                'tax_amount_formatted' => number_format($order->tax_amount, 2) . ' AED',
                'discount' => (float) ($order->discount ?? 0),
                'discount_formatted' => number_format($order->discount ?? 0, 2) . ' AED',
                'total' => (float) $order->total,
                'total_formatted' => number_format($order->total, 2) . ' AED',
            ],
            'coupon' => $order->coupon ? [
                'id' => $order->coupon->id,
                'code' => $order->coupon->code,
                'name' => $order->coupon->name,
                'type' => $order->coupon->type,
                'value' => $order->coupon->value,
                'discount_amount' => (float) ($order->discount ?? 0),
                'discount_amount_formatted' => number_format($order->discount ?? 0, 2) . ' AED',
            ] : null,
            'currency' => 'AED',
            'shipping_options_summary' => [
                'delivery_only' => [
                    'count' => $order->items->where('shipping_option', 'delivery_only')->count(),
                    'items' => $order->items->where('shipping_option', 'delivery_only')->map(fn($item) => [
                        'id' => $item->id,
                        'product_name' => $item->buyable->name ?? $item->buyable->title ?? $item->product_name,
                        'quantity' => $item->quantity,
                    ])->values(),
                ],
                'with_installation' => [
                    'count' => $order->items->where('shipping_option', 'with_installation')->count(),
                    'mobile_vans' => $order->items->where('shipping_option', 'with_installation')
                        ->whereNotNull('mobile_van_id')
                        ->groupBy('mobile_van_id')
                        ->map(function($items) {
                            $van = $items->first()->mobileVan;
                            return [
                                'id' => $van?->id,
                                'name' => $van?->name,
                                'location' => $van?->location,
                                'phone' => $van?->phone,
                                'items_count' => $items->count(),
                                'items' => $items->map(fn($item) => [
                                    'id' => $item->id,
                                    'product_name' => $item->buyable->name ?? $item->buyable->title ?? $item->product_name,
                                    'quantity' => $item->quantity,
                                    'installation_date' => $item->installation_date,
                                    'installation_date_formatted' => $item->installation_date ? \Carbon\Carbon::parse($item->installation_date)->format('d M Y, h:i A') : null,
                                ])->values(),
                            ];
                        })->values(),
                ],
                'installation_center' => [
                    'count' => $order->items->where('shipping_option', 'installation_center')->count(),
                    'centers' => $order->items->where('shipping_option', 'installation_center')
                        ->whereNotNull('installation_center_id')
                        ->groupBy('installation_center_id')
                        ->map(function($items) {
                            $center = $items->first()->installationCenter;
                            return [
                                'id' => $center?->id,
                                'name' => $center?->name,
                                'location' => $center?->location,
                                'city' => $center?->city,
                                'phone' => $center?->phone,
                                'google_maps_link' => $center?->google_maps_link,
                                'items_count' => $items->count(),
                                'items' => $items->map(fn($item) => [
                                    'id' => $item->id,
                                    'product_name' => $item->buyable->name ?? $item->buyable->title ?? $item->product_name,
                                    'quantity' => $item->quantity,
                                    'installation_date' => $item->installation_date,
                                    'installation_date_formatted' => $item->installation_date ? \Carbon\Carbon::parse($item->installation_date)->format('d M Y, h:i A') : null,
                                ])->values(),
                            ];
                        })->values(),
                ],
            ],
            'metadata' => [
                'items_count' => $order->items->count(),
                'total_quantity' => $order->items->sum('quantity'),
                'has_tracking' => !empty($order->tracking_number),
                'has_installation' => $order->items->where('shipping_option', 'with_installation')->count() > 0,
                'has_pickup' => $order->items->where('shipping_option', 'installation_center')->count() > 0,
            ],
        ];

        return response()->json($orderData);
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

}
