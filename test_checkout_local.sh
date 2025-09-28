#!/bin/bash

BASE_URL="http://localhost:8000"

echo "=== Testing Checkout Flow Locally ==="

# Step 1: Get a product ID
echo "1. Getting product ID..."
PRODUCT_RESPONSE=$(curl -s "$BASE_URL/api/batteries" | head -c 500)
echo "Product response: $PRODUCT_RESPONSE"
PRODUCT_ID=$(echo $PRODUCT_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
echo "Using product ID: $PRODUCT_ID"

# Step 2: Login and get token
echo -e "\n2. Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }')
echo "Login response: $LOGIN_RESPONSE"

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token: $TOKEN"

if [ -z "$TOKEN" ]; then
  echo "Failed to get token, trying registration..."
  REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/register" \
    -H "Content-Type: application/json" \
    -d '{
      "name": "Test User",
      "email": "test@example.com",
      "password": "password123",
      "password_confirmation": "password123"
    }')
  echo "Register response: $REGISTER_RESPONSE"
  TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
fi

if [ -z "$TOKEN" ]; then
  echo "ERROR: Could not get authentication token"
  exit 1
fi

# Step 3: Add item to cart
echo -e "\n3. Adding item to cart..."
CART_RESPONSE=$(curl -s -X POST "$BASE_URL/api/cart/add" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"buyable_type\": \"battery\",
    \"buyable_id\": $PRODUCT_ID,
    \"quantity\": 1
  }")
echo "Cart add response: $CART_RESPONSE"

# Step 4: Check cart
echo -e "\n4. Checking cart..."
CART_CHECK=$(curl -s "$BASE_URL/api/cart" \
  -H "Authorization: Bearer $TOKEN")
echo "Cart contents: $CART_CHECK"

# Step 5: Save address
echo -e "\n5. Saving address..."
ADDRESS_RESPONSE=$(curl -s -X POST "$BASE_URL/api/checkout/save-address" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "label": "Test Home",
    "full_name": "Test User",
    "phone": "+971501234567",
    "country_id": 1,
    "governorate_id": 1,
    "city_id": 1,
    "area_id": 1,
    "address_line": "Test Address",
    "is_primary": true
  }')
echo "Address save response: $ADDRESS_RESPONSE"

ADDRESS_ID=$(echo $ADDRESS_RESPONSE | grep -o '"id":[0-9]*' | cut -d':' -f2)
echo "Address ID: $ADDRESS_ID"

# Step 6: Try checkout
echo -e "\n6. Attempting checkout..."
CHECKOUT_RESPONSE=$(curl -s -X POST "$BASE_URL/api/checkout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"address_id\": $ADDRESS_ID,
    \"payment_method\": \"paymob\",
    \"car_make\": \"Honda\",
    \"car_model\": \"Civic\",
    \"car_year\": 2018,
    \"items\": [
      {
        \"buyable_type\": \"battery\",
        \"buyable_id\": $PRODUCT_ID,
        \"shipping_option\": \"delivery_only\"
      }
    ]
  }")
echo "Checkout response: $CHECKOUT_RESPONSE"

echo -e "\n=== Test Complete ==="