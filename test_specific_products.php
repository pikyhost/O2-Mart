<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WishlistController;
use App\Models\Rim;
use App\Models\Tyre;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Specific Products Price Resolution\n";
echo "============================================\n\n";

// Test the specific products mentioned in the user's example
$testProducts = [
    ['type' => 'rim', 'id' => 20, 'name' => 'Wheel Nation test'],
    ['type' => 'tyre', 'id' => 8732, 'name' => 'Yokohama 305/35R23'],
];

foreach ($testProducts as $product) {
    echo "📦 Testing {$product['type']} ID {$product['id']}: {$product['name']}\n";
    
    // Get the actual product from database
    if ($product['type'] === 'rim') {
        $model = Rim::find($product['id']);
    } else {
        $model = Tyre::find($product['id']);
    }
    
    if (!$model) {
        echo "   ❌ Product not found in database\n\n";
        continue;
    }
    
    echo "   📊 Database values:\n";
    if ($product['type'] === 'rim') {
        echo "      - regular_price: " . ($model->regular_price ?? 'null') . "\n";
        echo "      - discounted_price: " . ($model->discounted_price ?? 'null') . "\n";
        echo "      - is_set_of_4: " . ($model->is_set_of_4 ? 'true' : 'false') . "\n";
    } else {
        echo "      - price_vat_inclusive: " . ($model->price_vat_inclusive ?? 'null') . "\n";
        echo "      - discounted_price: " . ($model->discounted_price ?? 'null') . "\n";
        echo "      - is_set_of_4: " . ($model->is_set_of_4 ? 'true' : 'false') . "\n";
    }
    
    // Test the price resolution logic
    $controller = new WishlistController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('resolvePrice');
    $method->setAccessible(true);
    
    $resolvedPrice = $method->invoke($controller, $model);
    
    echo "   💰 Resolved price: AED " . number_format($resolvedPrice, 2) . "\n\n";
}

echo "✅ Price resolution test completed!\n";