<?php
// Test script for address endpoints
// Run with: php test_address_endpoint.php

echo "Testing Address Endpoints...\n\n";

// Test 1: Get addresses (should return empty for new user/guest)
echo "1. Testing GET /api/checkout/addresses\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/checkout/addresses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Session-Id: test-session-123'
]);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: " . $response . "\n\n";

// Test 2: Save new address
echo "2. Testing POST /api/checkout/save-address\n";
$addressData = [
    'label' => 'Test Home',
    'full_name' => 'Test User',
    'phone' => '+971501234567',
    'country_id' => 1,
    'governorate_id' => 1,
    'city_id' => 1,
    'area_id' => 1,
    'address_line' => '123 Test Street',
    'additional_info' => 'Near test mall',
    'is_primary' => true
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/checkout/save-address');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($addressData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Session-Id: test-session-123'
]);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: " . $response . "\n\n";

// Test 3: Get addresses again (should now return the saved address)
echo "3. Testing GET /api/checkout/addresses (after saving)\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/checkout/addresses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Session-Id: test-session-123'
]);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: " . $response . "\n\n";

echo "Test completed!\n";