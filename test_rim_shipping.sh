#!/bin/bash

echo "=== Testing Rim Shipping Cost Difference ==="

# Step 1: Add rim to cart
echo "Step 1: Adding rim to cart..."
curl -c cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/add" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 71,
    "quantity": 4
  }' -s

echo -e "\n"

# Step 2: Update shipping option
echo "Step 2: Setting shipping to delivery_only..."
curl -b cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/update-shipping-option" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 71,
    "shipping_option": "delivery_only"
  }' -s

echo -e "\n"

# Step 3: Test Area 196
echo "Step 3: Testing Area 196..."
curl -b cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/calculate-shipping" \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_shipments": 0,
    "area_id": 196
  }' -s

echo -e "\n"

echo "Step 4: Cart Summary for Area 196:"
curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s

echo -e "\n\n"

# Step 5: Test Area 203
echo "Step 5: Testing Area 203..."
curl -b cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/calculate-shipping" \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_shipments": 0,
    "area_id": 203
  }' -s

echo -e "\n"

echo "Step 6: Cart Summary for Area 203:"
curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s

echo -e "\n\n=== Test Complete ==="

# Cleanup
rm -f cookies.txt