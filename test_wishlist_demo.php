<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WishlistController;
use App\Services\WishlistService;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\AutoPart;
use App\Models\Battery;
use App\Models\Tyre;
use App\Models\Rim;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🛒 O2Mart Wishlist Demo\n";
echo "======================\n\n";

// Simulate session ID
$sessionId = 'demo-session-' . time();
echo "📱 Session ID: {$sessionId}\n\n";

// Create a mock request with session ID
$request = Request::create('/api/wishlist', 'POST', [], [], [], [
    'HTTP_X_SESSION_ID' => $sessionId,
    'HTTP_CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json'
]);

// Set the request globally
app()->instance('request', $request);

try {
    // Get some sample products
    echo "📦 Getting sample products...\n";
    
    $autoPart = AutoPart::first();
    $battery = Battery::first();
    $tyre = Tyre::first();
    $rim = Rim::first();
    
    if (!$autoPart || !$battery || !$tyre || !$rim) {
        echo "❌ Error: Not enough sample products found in database\n";
        echo "   Auto Parts: " . AutoPart::count() . "\n";
        echo "   Batteries: " . Battery::count() . "\n";
        echo "   Tyres: " . Tyre::count() . "\n";
        echo "   Rims: " . Rim::count() . "\n";
        exit(1);
    }
    
    $products = [
        ['type' => 'auto_part', 'id' => $autoPart->id, 'name' => $autoPart->name],
        ['type' => 'battery', 'id' => $battery->id, 'name' => $battery->name],
        ['type' => 'tyre', 'id' => $tyre->id, 'name' => $tyre->name],
        ['type' => 'rim', 'id' => $rim->id, 'name' => $rim->name],
    ];
    
    echo "✅ Found sample products:\n";
    foreach ($products as $product) {
        echo "   - {$product['type']}: {$product['name']} (ID: {$product['id']})\n";
    }
    echo "\n";
    
    // Initialize controller
    $controller = new WishlistController();
    
    echo "🔄 Adding products to wishlist...\n";
    
    // Add each product to wishlist
    foreach ($products as $product) {
        $addRequest = Request::create('/api/wishlist', 'POST', [
            'buyable_type' => $product['type'],
            'buyable_id' => $product['id']
        ], [], [], [
            'HTTP_X_SESSION_ID' => $sessionId,
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        
        app()->instance('request', $addRequest);
        
        $response = $controller->store($addRequest);
        $responseData = json_decode($response->getContent(), true);
        
        if ($response->getStatusCode() === 200) {
            echo "   ✅ Added {$product['type']}: {$product['name']}\n";
        } else {
            echo "   ❌ Failed to add {$product['type']}: " . ($responseData['message'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n📋 Viewing wishlist contents...\n";
    
    // View wishlist
    $viewRequest = Request::create('/api/wishlist', 'GET', [], [], [], [
        'HTTP_X_SESSION_ID' => $sessionId,
        'HTTP_ACCEPT' => 'application/json'
    ]);
    
    app()->instance('request', $viewRequest);
    
    $response = $controller->index();
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Wishlist loaded successfully!\n\n";
        echo "📊 Wishlist Summary:\n";
        echo "   Session ID: {$responseData['session_id']}\n";
        echo "   Total Items: " . count($responseData['wishlist']['items']) . "\n";
        echo "   Subtotal: AED " . number_format($responseData['wishlist']['subtotal'], 2) . "\n";
        echo "   Total: AED " . number_format($responseData['wishlist']['total'], 2) . "\n\n";
        
        echo "🛍️ Items in Wishlist:\n";
        foreach ($responseData['wishlist']['items'] as $index => $item) {
            echo "   " . ($index + 1) . ". {$item['type']}: {$item['name']}\n";
            echo "      Price: AED " . number_format($item['price_per_unit'], 2) . "\n";
            echo "      Quantity: {$item['quantity']}\n";
            echo "      Subtotal: AED " . number_format($item['subtotal'], 2) . "\n";
            if (isset($item['brand']) && $item['brand']) {
                echo "      Brand: {$item['brand']['name']}\n";
            }
            if (isset($item['image']) && $item['image']) {
                echo "      Image: Available\n";
            }
            echo "\n";
        }
    } else {
        echo "❌ Failed to load wishlist: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
    
    echo "🗑️ Testing product removal...\n";
    
    // Remove first product from wishlist
    if (!empty($products)) {
        $productToRemove = $products[0];
        
        $removeRequest = Request::create('/api/wishlist', 'DELETE', [
            'buyable_type' => $productToRemove['type'],
            'buyable_id' => $productToRemove['id']
        ], [], [], [
            'HTTP_X_SESSION_ID' => $sessionId,
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        
        app()->instance('request', $removeRequest);
        
        $response = $controller->destroy($removeRequest);
        $responseData = json_decode($response->getContent(), true);
        
        if ($response->getStatusCode() === 200) {
            echo "   ✅ Removed {$productToRemove['type']}: {$productToRemove['name']}\n";
        } else {
            echo "   ❌ Failed to remove product: " . ($responseData['message'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n📋 Final wishlist state...\n";
    
    // View wishlist again
    $finalViewRequest = Request::create('/api/wishlist', 'GET', [], [], [], [
        'HTTP_X_SESSION_ID' => $sessionId,
        'HTTP_ACCEPT' => 'application/json'
    ]);
    
    app()->instance('request', $finalViewRequest);
    
    $response = $controller->index();
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Final wishlist state:\n";
        echo "   Total Items: " . count($responseData['wishlist']['items']) . "\n";
        echo "   Total: AED " . number_format($responseData['wishlist']['total'], 2) . "\n\n";
        
        foreach ($responseData['wishlist']['items'] as $index => $item) {
            echo "   " . ($index + 1) . ". {$item['type']}: {$item['name']} - AED " . number_format($item['price_per_unit'], 2) . "\n";
        }
    }
    
    echo "\n🎉 Wishlist demo completed successfully!\n";
    echo "\n📝 API Endpoints Summary:\n";
    echo "   POST /api/wishlist - Add product to wishlist\n";
    echo "   GET /api/wishlist - View wishlist contents\n";
    echo "   DELETE /api/wishlist - Remove product from wishlist\n";
    echo "\n🔧 Required Headers:\n";
    echo "   X-Session-ID: your-session-id (for guest users)\n";
    echo "   Authorization: Bearer token (for authenticated users)\n";
    echo "   Content-Type: application/json\n";
    echo "   Accept: application/json\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}