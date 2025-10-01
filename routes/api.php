<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\PopupController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierPageController;
use App\Http\Middleware\CheckAuthOrSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressBookController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AutoPartController;
use App\Http\Controllers\Api\BatteryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RimController;
use App\Http\Controllers\Api\TyreController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\ProductSectionController;
use App\Http\Controllers\Api\ShopPageController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\UserVehicleController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\PaymobController;
use App\Services\PaymobPaymentService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

require __DIR__.'/auth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
return $request->user();
});
Route::get('/session-id', function () {
return response()->json(['session_id' => session()->getId()]);
});

Route::get('/home', [HomeController::class, 'index']);
Route::get('/product-sections/{type}', [ProductSectionController::class, 'show']);

Route::get('/shop-page', [ShopPageController::class, 'index']);

Route::get('/about-us', [AboutUsController::class, 'index'])->name('api.about-us.index');

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);

Route::post('/forgot-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'resetPassword']);
Route::get('/password/validate', [\App\Http\Controllers\Api\PasswordResetController::class, 'validateToken']);

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/faqs', [FaqController::class, 'index']);

Route::get('/contact-us', [\App\Http\Controllers\Api\ContactUsPageController::class, 'index']);

Route::post('/contact', [ContactMessageController::class, 'store']);

Route::post('/newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');

Route::get('/policy/privacy', [PolicyController::class, 'privacy']);
Route::get('/policy/refund', [PolicyController::class, 'refund']);
Route::get('/policy/terms', [PolicyController::class, 'terms']);

Route::get('/supplier-page', [SupplierPageController::class, 'show']);
Route::post('/suppliers', [SupplierController::class, 'store']);

Route::middleware('api')->group(function () {
// Public blog endpoints
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/categories', [BlogController::class, 'categories']);
Route::get('/blogs/recent', [BlogController::class, 'recent']);
Route::get('/blogs/search', [BlogController::class, 'search']);
Route::get('/blogs/category/{categoryId}', [BlogController::class, 'byCategory']);
Route::get('/blogs/tag/{tagId}', [BlogController::class, 'byTag']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);
Route::get('/tags', [BlogController::class, 'getTags']);

// Authenticated endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blogs/{blogId}/like', [BlogController::class, 'toggleLike']);
});
});

Route::middleware('auth:sanctum')->group(function () {
Route::get('/account', [AccountController::class, 'show']);
Route::put('/account', [AccountController::class, 'update']);
Route::put('/account/password', [AccountController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->prefix('garage')->group(function () {
Route::get('/', [UserVehicleController::class, 'index']);
Route::post('/', [UserVehicleController::class, 'store']);
Route::put('/{id}', [UserVehicleController::class, 'update']);
Route::delete('/{id}', [UserVehicleController::class, 'destroy']);
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
$request->fulfill();

return redirect('http://localhost:3000/email-verified');
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::prefix('brands')->group(function () {
Route::get('/', [BrandController::class, 'index']); 
Route::get('/all', [BrandController::class, 'all']);
Route::get('/by-type', [BrandController::class, 'allByType']);
Route::get('/active', [BrandController::class, 'active']);
Route::get('/{slug}', [BrandController::class, 'show']);

});
Route::post('/email/verification-notification', function (Request $request) {
if ($request->user()->hasVerifiedEmail()) {
    return response()->json(['message' => 'Email already verified.'], 400);
}

$request->user()->sendEmailVerificationNotification();

return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::get('/car-makes', [VehicleController::class, 'carMakesWithModelsAndYears']);
Route::get('/car-makes/{id}/models', [VehicleController::class, 'getModelsByMake']);
Route::get('/car-models/{id}/years', [VehicleController::class, 'getModelYears']);

// Location Routes (no auth required)
Route::get('/locations/countries', [LocationController::class, 'countries']);
Route::get('/locations/countries/{country}/cities', [LocationController::class, 'citiesByCountry']);
Route::get('/locations/countries/{country}/governorates', [LocationController::class, 'governorates']);
Route::get('/locations/governorates/{governorate}/cities', [LocationController::class, 'cities']);
Route::get('/locations/cities/{city}/areas', [LocationController::class, 'areas']);

Route::middleware(CheckAuthOrSession::class)->group(function () {
Route::get('/addresses', [AddressBookController::class, 'index']);
Route::post('/addresses', [AddressBookController::class, 'store']);
Route::post('/addresses/{id}/make-primary', [AddressBookController::class, 'makePrimary']);
Route::get('/addresses/primary/default', [AddressBookController::class, 'getPrimary']);
Route::get('/addresses/{id}', [AddressBookController::class, 'show']);
Route::put('/addresses/{id}', [AddressBookController::class, 'update']);
Route::delete('/addresses/{id}', [AddressBookController::class, 'destroy']);

});

// Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/filter', [ProductController::class, 'filter']);
Route::get('/user/last-filter', function () {
return response()->json(['data' => request()->user()->last_filter]);
})->middleware('auth:sanctum');
// Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
// Route::get('/attributes', [AttributeController::class, 'index']);
// Route::get('/products/part-number/search', [ProductController::class, 'searchByPartNumber']);
// Route::get('/products/cross-reference/search', [ProductController::class, 'findByCrossReference']);

// Route::middleware('api')->group(function () {
//     Route::get('/products/{productId}/compatible-vehicles', [ProductController::class, 'getCompatibleVehicles']);
//     Route::get('/products/check-compatibility', [ProductController::class, 'checkCompatibility']);
// });

// Route::get('/recommendations', [ProductController::class, 'getRecommendations']);

Route::prefix('auto-parts')->group(function () {
Route::get('/', [AutoPartController::class, 'index']);
Route::get('/filters', [AutoPartController::class, 'filters']);
Route::get('/categories', [AutoPartController::class, 'categoriesWithSub']);
Route::get('/brands', [AutoPartController::class, 'brands']);
Route::get('/countries', [AutoPartController::class, 'countries']);
Route::get('/{id}', [AutoPartController::class, 'show']);
Route::get('/part-number/search', [AutoPartController::class, 'searchByPartNumber']);
Route::get('/cross-reference/search', [AutoPartController::class, 'findByCrossReference']);
});

Route::prefix('tyres')->group(function () {
Route::get('/', [TyreController::class, 'index']);
Route::get('/compatible-car-makes', [TyreController::class, 'compatibleCarMakes']);
Route::get('/compatible-sizes', [TyreController::class, 'getCompatibleSizes']);
Route::get('/filters', [TyreController::class, 'filters']);
Route::get('/attributes-by-car-trim', [TyreController::class, 'getAttributesByCarAndTrim']);
Route::get('/filter-by-car-trim', [TyreController::class, 'filterByCarAndTrim']);
Route::get('/attribute/search', [TyreController::class, 'searchByAttribute']);
Route::get('/oem/search', [TyreController::class, 'searchByOem']);
Route::get('/trims', [TyreController::class, 'trims']);
Route::get('/search-by-size-options', [TyreController::class, 'searchBySizeOptions']);

Route::get('/{id}', [TyreController::class, 'show']);
});
Route::get('/rim-sizes', fn () => \App\Models\RimSize::select('id', 'size')->get());

Route::middleware(CheckAuthOrSession::class)->prefix('compare')->group(function () {
Route::get('/', [CompareController::class, 'index']);
Route::post('/add', [CompareController::class, 'add']);
Route::delete('/remove', [CompareController::class, 'remove']);
Route::post('/clear', [CompareController::class, 'clear']);
Route::get('/debug', [CompareController::class, 'debug']);
});

Route::prefix('batteries')->group(function () {
Route::get('/part-number/search', [BatteryController::class, 'searchByPartNumber']);
Route::get('/', [BatteryController::class, 'index']);
Route::get('/filters', [BatteryController::class, 'filters']);
Route::get('/compatible-search', [BatteryController::class, 'compatibleSearch']);
Route::get('/compatible-car-makes', [BatteryController::class, 'compatibleCarMakes']);
Route::get('/{id}', [BatteryController::class, 'show']);
});

Route::prefix('rims')->group(function () {
Route::get('/', [RimController::class, 'index']);
Route::get('/filters', [RimController::class, 'filters']);
Route::get('/compatible-search', [RimController::class, 'compatibleSearch']);
Route::get('/compatible-car-makes', [RimController::class, 'compatibleCarMakes']);
Route::get('/{id}', [RimController::class, 'show']);
Route::get('/part-number/search', [RimController::class, 'searchByPartNumber']);
});

Route::post('/reviews', [ProductReviewController::class, 'store']);
Route::get('/reviews', [ProductReviewController::class, 'getAllReviews']);


// Route
Route::middleware(CheckAuthOrSession::class)->group(function () {
Route::get('/wishlist', [WishlistController::class, 'index']);
Route::post('/wishlist', [WishlistController::class, 'store']);
Route::delete('/wishlist', [WishlistController::class, 'destroy']);
});


Route::prefix('compatibility')->group(function () {
Route::get('/{type}/makes', [VehicleController::class, 'getCompatibleMakes']);
Route::get('/{type}/models', [VehicleController::class, 'getCompatibleModels']);
Route::get('/{type}/years', [VehicleController::class, 'getCompatibleYears']);
});

Route::middleware(CheckAuthOrSession::class)->group(function () {
Route::get('/cart-menu', [CartController::class, 'getCartMenu']);
Route::get('/cart', [CartController::class, 'getCart']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/add-tyre-group', [CartController::class, 'addTyreGroup']);
Route::delete('/cart/remove', [CartController::class, 'remove']);
Route::get('/cart/recommendations', [CartController::class, 'getRecommendations']);
Route::put('/cart/update-quantity', [CartController::class, 'updateQuantity']);
Route::post('/cart/calculate-shipping', [CartController::class, 'calculateShipping']);
Route::get('/checkout/installer-shops', [CartController::class, 'getInstallerShops']);
Route::get('/checkout/mobile-vans', [CartController::class, 'getMobileVanServices']);
Route::get('/checkout/installation-dates', [CartController::class, 'getAvailableInstallationDates']);
Route::put('/cart/update-shipping-option', [CartController::class, 'updateShippingOption']);
Route::put('/cart/update-area', [CheckoutController::class, 'updateCartArea']);
Route::get('/cart/summary', [CartController::class, 'getCartSummary']);
});
Route::post('/checkout/guest', [CheckoutController::class, 'guestCheckout']);
Route::get('/guest/tracking/{id}', [CheckoutController::class, 'getGuestTracking']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/cart/shipping-breakdown', [CartController::class, 'getShippingBreakdown']);
});

Route::middleware('auth:sanctum')->get('/orders/user', [CheckoutController::class, 'getUserOrders']);
Route::middleware('auth:sanctum')->get('/orders/view/{id}', function ($id) {
$order = \App\Models\Order::with('items')->findOrFail($id);

if (auth()->id() !== $order->user_id) {
    return response()->json(['message' => 'Unauthorized'], 403);
}

return response()->json([
    'order' => $order,
]);
})->name('api.orders.view');
Route::get('/track', [TrackingController::class, 'track']);

// Route::post('/payment/paymob/callback', function (\Illuminate\Http\Request $request) {
//     $service = new PaymobPaymentService();
//     $success = $service->callBack($request);
//     return response()->json(['success' => $success]);
// });
Route::post('/api/payment/paymob/initiate', [PaymobController::class, 'initiate']);

Route::post('/payment/paymob/webhook', [PaymobController::class, 'handleWebhook']);
Route::post('/payment/paymob/callback', [PaymobController::class, 'handleWebhook'])->name('paymob.callback');


Route::get('/payment/redirect', [PaymobController::class, 'handleRedirect']);
Route::get('/track/{tracking_number}', [TrackingController::class, 'trackByNumber']);


Route::middleware('auth:sanctum')
->group(function () {
Route::post('/checkout', [CheckoutController::class, 'userCheckout']);
Route::get('/account', [AccountController::class, 'show']);
});

// Checkout addresses and save address for auth users only
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/checkout/addresses', [CheckoutController::class, 'getCheckoutAddresses']);
    Route::post('/checkout/save-address', [CheckoutController::class, 'saveCheckoutAddress']);
});
Route::middleware('auth:sanctum')->get('/orders/{order}/tracking', [CheckoutController::class, 'getTracking']);

// Authenticated only routes
Route::middleware('auth:sanctum')->group(function () {
Route::post('/addresses/transfer-to-user', [AddressBookController::class, 'transferToUser']);
});

Route::post('/inquiries', [InquiryController::class, 'store']);
Route::get('/inquiries/debug', [InquiryController::class, 'debug']);
Route::get('/inquiries/types', function () {
return response()->json([
    'types' => \App\Models\Inquiry::TYPES
]);
});


Route::get('/popups', [App\Http\Controllers\Api\PopupController::class, 'index'])->name('popups.index');

Route::post('/popups/submit-email', [PopupController::class, 'submitEmail']);
Route::post('/cart/apply-coupon', [CouponController::class, 'apply']);
Route::delete('/cart/remove-coupon', [CouponController::class, 'remove']);

/*
https://mk3bel.o2mart.net/api/cart/add
{
    "buyable_type": "battery",
    "buyable_id": 178,
    "quantity": 1
}

https://mk3bel.o2mart.net/api/cart/update-shipping-option
{
    "buyable_type": "auto_part",
    "buyable_id": 560,
    "shipping_option": "with_installation",
    "mobile_van_id": 4,
    "installation_date": "2025-09-11"
}

https://mk3bel.o2mart.net/api/cart/update-area
{
    "area_id": 1
}

//https://o2mart.to7fa.online/api/cart/apply-coupon
{
    "coupon_code": "SAVE20",
    "area_id": 1
}


https://mk3bel.o2mart.net/api/cart/add
{
    "buyable_type": "auto_part",
    "buyable_id": 560,
    "quantity": 1
}

https://mk3bel.o2mart.net/api/cart/update-shipping-option
{
    "buyable_type": "auto_part",
    "buyable_id": 560,
    "shipping_option": "with_installation",
    "mobile_van_id": 4,
    "installation_date": "2025-09-11"
}

https://mk3bel.o2mart.net/api/cart/update-area
{
    "area_id": 1
}

//https://o2mart.to7fa.online/api/cart/apply-coupon
{
    "coupon_code": "SAVE20",
    "area_id": 1
}



    {
  "title": "MR.",
  "first_name": "Mohamed Guest",
  "last_name": "G.",
  "email": "guest@example.com",
  "mobile": "0501234567",
  "country_id": 1,
  "governorate_id": 1,
  "city_id": 2,
  "area_id": 1,
  "address_name": "Home",
  "address_line": "Villa 12, Palm Jumeirah",
  "shipping_method": "jeebly",
  "payment_method": "paymob",
  "notes": "Please handle each item as per shipping option.",
  "car_make": "Honda",
  "car_model": "Civic",
  "car_year": "2018",
  "plate_number": "UAE5678",
  "vin": "2HGFC2F59JH123456",
  "items": [
    {
      "buyable_type": "battery",
      "buyable_id": 178,
      "shipping_option": "delivery_only"
    },
    
  ]
}


*/

// "shipping_cost": 21.53, from where this return ??

/*

1- total = quantity * price (pricing includeing the vat already)
2 - subtotal = total * vat_percentage / 100

3- at cart page subtotal of product same = subtotal and same 
and the summary = sum of the itmes subtotal

4- at cart page total = each item price * quantiy + shipping option (shipping setting installation fees)
5- couppon discount = appliy at total direct

6 - at checkout page  subtoal = cart page subtoal = cart menu subtotal
7 - shipping at checkout is 0 when the shipping option is installation center
shiping at checkout 0 when the option is with delivery with insatllation or 
installation center

but the first option  = calculation cost + city cost



endpoint of frot pages:
1 - /cart-menu  new for cart men 
2- cart pate is /cart
3 - checkout page is /cart/summary


// vat = total - subtoal at checkout page .
// sutotao of car titem withoutvat at each product at cart 

// at the cart page , that use the /api/cart
I want to make hte subtotal of 

*/