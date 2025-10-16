<?php

require_once 'vendor/autoload.php';

use App\Services\JeeblyService;
use App\Models\Order;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test tracking a specific order
$trackingNumber = 'JB304162'; // Replace with actual tracking number

$jeeblyService = new JeeblyService();

echo "Testing Jeebly tracking for: {$trackingNumber}\n";
echo "=====================================\n";

$trackingData = $jeeblyService->trackShipment($trackingNumber);

if ($trackingData) {
    echo "✅ Tracking data retrieved successfully\n";
    echo "Raw response:\n";
    echo json_encode($trackingData, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($trackingData['Tracking']['last_status'])) {
        $lastStatus = $trackingData['Tracking']['last_status'];
        echo "Last status: {$lastStatus}\n";
        
        // Test status mapping
        $reflection = new ReflectionClass($jeeblyService);
        $mapOrderStatus = $reflection->getMethod('mapJeeblyStatusToOrderStatus');
        $mapOrderStatus->setAccessible(true);
        $mapShippingStatus = $reflection->getMethod('mapJeeblyStatusToShippingStatus');
        $mapShippingStatus->setAccessible(true);
        
        $orderStatus = $mapOrderStatus->invoke($jeeblyService, $lastStatus);
        $shippingStatus = $mapShippingStatus->invoke($jeeblyService, $lastStatus);
        
        echo "Mapped order status: {$orderStatus}\n";
        echo "Mapped shipping status: {$shippingStatus}\n";
    }
} else {
    echo "❌ Failed to retrieve tracking data\n";
}

echo "\n=====================================\n";
echo "Test completed\n";