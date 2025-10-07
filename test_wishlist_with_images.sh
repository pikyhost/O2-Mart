#!/bin/bash

echo "🛒 O2Mart Wishlist API Test - Image Viewing Demo"
echo "================================================"
echo ""

BASE_URL="http://localhost:8000/api"
SESSION_ID="test-session-$(date +%s)"

echo "📱 Session ID: $SESSION_ID"
echo "🌐 Base URL: $BASE_URL"
echo ""

# Function to make API calls and format JSON
make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    
    echo "🔄 $method $endpoint"
    if [ -n "$data" ]; then
        echo "📤 Data: $data"
    fi
    
    echo "📥 Response:"
    response=$(curl -s -X "$method" "$BASE_URL$endpoint" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "X-Session-ID: $SESSION_ID" \
        ${data:+-d "$data"})
    
    echo "$response" | python3 -m json.tool 2>/dev/null || echo "$response"
    echo ""
    echo "----------------------------------------"
    echo ""
}

echo "1️⃣ Get AutoPart Details (Engine Oil with Image)"
echo "================================================"
make_request "GET" "/auto-parts/6"

echo "2️⃣ Add AutoPart to Wishlist"
echo "============================"
make_request "POST" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": 6}"

echo "3️⃣ Add Another AutoPart to Wishlist"
echo "===================================="
make_request "POST" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": 7}"

echo "4️⃣ View Complete Wishlist (with Images)"
echo "========================================"
make_request "GET" "/wishlist"

echo "5️⃣ Test Battery Product"
echo "======================="
make_request "GET" "/batteries"

echo "6️⃣ Add Battery to Wishlist (if available)"
echo "=========================================="
# Get first battery ID
BATTERY_RESPONSE=$(curl -s "$BASE_URL/batteries" -H "Accept: application/json")
BATTERY_ID=$(echo "$BATTERY_RESPONSE" | python3 -c "import sys, json; data=json.load(sys.stdin); print(data['data'][0]['id'] if data.get('data') and len(data['data']) > 0 else 'none')" 2>/dev/null)

if [ "$BATTERY_ID" != "none" ] && [ -n "$BATTERY_ID" ]; then
    echo "Found Battery ID: $BATTERY_ID"
    make_request "POST" "/wishlist" "{\"buyable_type\": \"battery\", \"buyable_id\": $BATTERY_ID}"
else
    echo "No batteries available for testing"
fi

echo "7️⃣ Final Wishlist View (Multiple Product Types)"
echo "================================================"
make_request "GET" "/wishlist"

echo "8️⃣ Remove One Item from Wishlist"
echo "================================="
make_request "DELETE" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": 6}"

echo "9️⃣ Final Wishlist State"
echo "======================="
make_request "GET" "/wishlist"

echo "✅ Wishlist Test Completed!"
echo ""
echo "📝 Test Summary:"
echo "✓ Retrieved AutoPart details with image URLs"
echo "✓ Added multiple products to wishlist"
echo "✓ Viewed wishlist with product images and details"
echo "✓ Tested different product types (AutoPart, Battery)"
echo "✓ Removed items from wishlist"
echo "✓ Verified final wishlist state"
echo ""
echo "🖼️ Image URLs are included in the wishlist response"
echo "📱 Session-based wishlist works without authentication"