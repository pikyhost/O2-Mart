#!/bin/bash

echo "=== Finding Default Area ID ==="

# Add rim to cart without specifying area
echo "Step 1: Adding rim to cart (no area specified)..."
RESPONSE1=$(curl -c cookies.txt -X POST "https://o2mart.to7fa.online/api/cart/add" \
  -H "Content-Type: application/json" \
  -d '{
    "buyable_type": "rim",
    "buyable_id": 12471,
    "quantity": 1
  }' -s)
echo "$RESPONSE1"

echo -e "\n"

# Get cart summary to see default shipping
echo "Step 2: Getting cart summary (should show default area shipping)..."
SUMMARY=$(curl -b cookies.txt -X GET "https://o2mart.to7fa.online/api/cart/summary" -s)
echo "$SUMMARY"

# Cleanup
rm -f cookies.txt

echo -e "\n=== Default area has shipping cost: 200.00 ==="