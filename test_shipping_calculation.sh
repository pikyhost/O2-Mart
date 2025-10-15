#!/bin/bash

# Test shipping calculation workflow
BASE_URL="http://localhost:8000/api"
COOKIE_FILE="test_cookies.txt"

echo "=== Testing Shipping Calculation Workflow ==="

# Step 1: Get session by calling cart endpoint
echo "Step 1: Getting session..."
curl -c $COOKIE_FILE -X GET "$BASE_URL/cart" -s

# Step 2: Add a product to cart
echo -e "\nStep 2: Adding product to cart..."
curl -b $COOKIE_FILE -X POST "$BASE_URL/cart/add" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "tyre",
    "buyable_id": 1,
    "quantity": 2
  }' -s

# Step 3: Update shipping option to delivery_only
echo -e "\nStep 3: Updating shipping option to delivery_only..."
curl -b $COOKIE_FILE -X POST "$BASE_URL/cart/update-shipping-option" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "tyre",
    "buyable_id": 1,
    "shipping_option": "delivery_only"
  }' -s

# Step 4: Get cart to see current state
echo -e "\nStep 4: Getting cart state before shipping calculation..."
curl -b $COOKIE_FILE -X GET "$BASE_URL/cart" -s

# Step 5: Calculate shipping with area that has cost
echo -e "\nStep 5: Calculating shipping with area that has cost..."
curl -b $COOKIE_FILE -X POST "$BASE_URL/cart/calculate-shipping" \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_shipments": 0,
    "area_id": 2
  }' -s

# Step 6: Get final cart state
echo -e "\nStep 6: Getting final cart state..."
curl -b $COOKIE_FILE -X GET "$BASE_URL/cart" -s

# Cleanup
rm -f $COOKIE_FILE

echo -e "\n=== Test Complete ==="