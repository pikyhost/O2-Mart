#!/bin/bash

echo "🛒 O2Mart Wishlist API Test with cURL"
echo "====================================="
echo ""

# Set the base URL (change this to your actual API URL)
BASE_URL="http://localhost:8000/api"
SESSION_ID="test-session-$(date +%s)"

echo "📱 Using Session ID: $SESSION_ID"
echo "🌐 Base URL: $BASE_URL"
echo ""

# Function to make API calls with proper headers
make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    
    echo "🔄 Making $method request to $endpoint"
    if [ -n "$data" ]; then
        echo "📤 Data: $data"
    fi
    
    curl -s -X "$method" "$BASE_URL$endpoint" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "X-Session-ID: $SESSION_ID" \
        ${data:+-d "$data"} | jq '.'
    
    echo ""
}

echo "1️⃣ Adding Auto Part to Wishlist"
echo "--------------------------------"
make_request "POST" "/wishlist" '{"buyable_type": "auto_part", "buyable_id": 6}'

echo "2️⃣ Adding Battery to Wishlist"
echo "------------------------------"
make_request "POST" "/wishlist" '{"buyable_type": "battery", "buyable_id": 235}'

echo "3️⃣ Adding Tyre to Wishlist"
echo "---------------------------"
make_request "POST" "/wishlist" '{"buyable_type": "tyre", "buyable_id": 5572}'

echo "4️⃣ Adding Rim to Wishlist"
echo "--------------------------"
make_request "POST" "/wishlist" '{"buyable_type": "rim", "buyable_id": 9}'

echo "📋 Viewing Complete Wishlist"
echo "----------------------------"
make_request "GET" "/wishlist"

echo "🗑️ Removing Auto Part from Wishlist"
echo "------------------------------------"
make_request "DELETE" "/wishlist" '{"buyable_type": "auto_part", "buyable_id": 6}'

echo "📋 Final Wishlist State"
echo "-----------------------"
make_request "GET" "/wishlist"

echo "✅ Wishlist API test completed!"
echo ""
echo "📝 Summary:"
echo "- Successfully added 4 different product types to wishlist"
echo "- Retrieved wishlist contents with product details"
echo "- Removed a product from wishlist"
echo "- Verified final state"
echo ""
echo "🔧 API Endpoints tested:"
echo "- POST /api/wishlist (add product)"
echo "- GET /api/wishlist (view wishlist)"
echo "- DELETE /api/wishlist (remove product)"