<?php

require_once 'vendor/autoload.php';

use App\Models\Area;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Tyre;
use App\Services\ShippingCalculatorService;

// Create Laravel app instance
$app = require_once 'bootstrap/app.php';

echo "=== Manual Shipping Calculation Test ===\n";

// Step 1: Find an area with shipping cost
$area = Area::whereNotNull('shipping_cost')
    ->where('shipping_cost', '>', 0)
    ->first();

if (!$area) {
    echo "No areas found with shipping cost\n";
    exit(1);
}

echo "Found area: {$area->name} with shipping cost: {$area->shipping_cost}\n";

// Step 2: Find a tyre to test with
$tyre = Tyre::first();
if (!$tyre) {
    echo "No tyres found\n";
    exit(1);
}

echo "Using tyre: {$tyre->title} with price: {$tyre->price_vat_inclusive}\n";

// Step 3: Create a mock cart
$cart = new Cart();
$cart->session_id = 'test_session';
$cart->area_id = $area->id;
$cart->subtotal = 100.00;
$cart->total = 100.00;

// Create a mock cart item
$cartItem = new CartItem();
$cartItem->buyable_type = 'tyre';
$cartItem->buyable_id = $tyre->id;
$cartItem->quantity = 2;
$cartItem->price_per_unit = $tyre->price_vat_inclusive;
$cartItem->subtotal = $tyre->price_vat_inclusive * 2;

// Add the item to cart (mock relationship)
$cart->setRelation('items', collect([$cartItem]));

echo "\nCart before shipping calculation:\n";
echo "- Subtotal: {$cart->subtotal}\n";
echo "- Total: {$cart->total}\n";
echo "- Area ID: {$cart->area_id}\n";
echo "- Shipping Cost: " . ($cart->shipping_cost ?? 0) . "\n";

// Step 4: Calculate shipping
echo "\nCalculating shipping...\n";
$shipping = ShippingCalculatorService::calculate($cart, 0); // 0 monthly shipments

if (!empty($shipping['error'])) {
    echo "Shipping calculation failed: " . ($shipping['message'] ?? 'Unknown error') . "\n";
    exit(1);
}

echo "Shipping calculation result:\n";
echo "- Shipping cost: {$shipping['total']}\n";
echo "- Breakdown: " . json_encode($shipping['breakdown'], JSON_PRETTY_PRINT) . "\n";

// Step 5: Update cart with shipping
$newTotal = $cart->subtotal + $shipping['total'];
$cart->shipping_cost = $shipping['total'];
$cart->total = $newTotal;

echo "\nCart after shipping calculation:\n";
echo "- Subtotal: {$cart->subtotal}\n";
echo "- Shipping Cost: {$cart->shipping_cost}\n";
echo "- Total: {$cart->total}\n";

echo "\n=== Test Complete ===\n";