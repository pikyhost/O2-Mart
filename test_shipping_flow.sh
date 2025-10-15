#!/bin/bash

echo "=== Testing Shipping Flow ==="

# Step 1: Add rim to cart
echo "Step 1: Adding rim 12471 to cart..."
RESPONSE1=$(curl -c cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/add" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 12471,
    "quantity": 1
  }' -s)
echo "$RESPONSE1"

echo -e "\n"

# Step 2: Update shipping option to delivery_only
echo "Step 2: Updating shipping option to delivery_only..."
RESPONSE2=$(curl -b cookies.txt -X PUT "https://o2mart.to7fa.online/api/cart/update-shipping-option" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 12471,
    "shipping_option": "delivery_only"
  }' -s)
echo "$RESPONSE2"

echo -e "\n"

# Step 3: Get cart summary (should show calculated shipping)
echo "Step 3: Getting cart summary after shipping option update..."
SUMMARY1=$(curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s)
echo "$SUMMARY1"

echo -e "\n"

# Step 4: Update area to 203
echo "Step 4: Updating area to 203..."
RESPONSE4=$(curl -b cookies.txt -X PUT "https://o2mart.to7fa.online/api/cart/update-area" \
  -H "Content-Type: application/json" \
  -d '{
    "area_id": 203
  }' -s)
echo "$RESPONSE4"

echo -e "\n"

# Step 5: Get cart summary after area update
echo "Step 5: Getting cart summary after area update..."
SUMMARY2=$(curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s)
echo "$SUMMARY2"

# Cleanup
rm -f cookies.txt

echo -e "\n=== Test Complete ==="