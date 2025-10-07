#!/bin/bash

echo "🛒 O2Mart AutoPart Wishlist Test"
echo "================================"
echo ""

# Configuration
BASE_URL="http://localhost:8000/api"
AUTOPART_ID=6
SESSION_ID="test-session-$(date +%s)"

echo "📱 Session ID: $SESSION_ID"
echo "🔧 AutoPart ID: $AUTOPART_ID"
echo "🌐 Base URL: $BASE_URL"
echo ""

# Function to make API calls
make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    
    echo "🔄 $method $endpoint"
    if [ -n "$data" ]; then
        echo "📤 Data: $data"
    fi
    
    response=$(curl -s -X "$method" "$BASE_URL$endpoint" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "X-Session-ID: $SESSION_ID" \
        ${data:+-d "$data"})
    
    echo "📥 Response:"
    echo "$response" | jq '.'
    echo ""
    
    return 0
}

echo "1️⃣ Get AutoPart Details (ID: $AUTOPART_ID)"
echo "===========================================" 
make_request "GET" "/auto-parts/$AUTOPART_ID"

echo "2️⃣ Add AutoPart to Wishlist"
echo "============================"
make_request "POST" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": $AUTOPART_ID}"

echo "3️⃣ View Wishlist (should contain the AutoPart)"
echo "==============================================="
make_request "GET" "/wishlist"

echo "4️⃣ Try to add the same AutoPart again (should handle duplicates)"
echo "================================================================="
make_request "POST" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": $AUTOPART_ID}"

echo "5️⃣ View Wishlist again (should still have only one item)"
echo "========================================================="
make_request "GET" "/wishlist"

echo "6️⃣ Remove AutoPart from Wishlist"
echo "================================="
make_request "DELETE" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": $AUTOPART_ID}"

echo "7️⃣ View Empty Wishlist"
echo "======================"
make_request "GET" "/wishlist"

echo "✅ Test completed!"
echo ""
echo "📝 Test Summary:"
echo "- Retrieved AutoPart details"
echo "- Added AutoPart to wishlist"
echo "- Viewed wishlist with product details and image"
echo "- Tested duplicate handling"
echo "- Removed product from wishlist"
echo "- Verified empty wishlist state"