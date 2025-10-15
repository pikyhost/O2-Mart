#!/bin/bash

echo "=== Testing Rim ID 12471 Shipping Cost: Area 196 vs 203 ==="

# Step 1: Add rim to cart
echo "Step 1: Adding rim 12471 (quantity 4) to cart..."
RESPONSE1=$(curl -c cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/add" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 12471,
    "quantity": 4
  }' -s)
echo "$RESPONSE1"

echo -e "\n"

# Step 2: Update shipping option
echo "Step 2: Setting shipping to delivery_only..."
RESPONSE2=$(curl -b cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/update-shipping-option" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 12471,
    "shipping_option": "delivery_only"
  }' -s)
echo "$RESPONSE2"

echo -e "\n"

# Step 3: Test Area 196
echo "Step 3: Testing Area 196..."
RESPONSE3=$(curl -b cookies.txt -X PUT "https://o2mart.to7fa.online/api/cart/update-area" \
  -H "Content-Type: application/json" \
  -d '{
    "area_id": 196
  }' -s)
echo "$RESPONSE3"

echo -e "\n"

echo "Step 4: Cart Summary for Area 196:"
SUMMARY196=$(curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s)
echo "$SUMMARY196"

echo -e "\n\n"

# Step 5: Test Area 203
echo "Step 5: Testing Area 203..."
RESPONSE5=$(curl -b cookies.txt -X PUT "https://o2mart.to7fa.online/api/cart/update-area" \
  -H "Content-Type: application/json" \
  -d '{
    "area_id": 203
  }' -s)
echo "$RESPONSE5"

echo -e "\n"

echo "Step 6: Cart Summary for Area 203:"
SUMMARY203=$(curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s)
echo "$SUMMARY203"

echo -e "\n\n=== ANALYSIS ==="
echo "Expected shipping costs:"
echo "- Area 196: 408.57"
echo "- Area 203: 223.44"
echo "- Difference: 185.13"

# Cleanup
rm -f cookies.txt

echo -e "\n=== Test Complete ==="