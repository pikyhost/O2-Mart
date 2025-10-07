#!/bin/bash

echo "🛒 O2Mart AutoPart Wishlist Test"
echo "================================"
echo ""

BASE_URL="http://localhost:8000/api"
AUTOPART_ID=6
SESSION_ID="test-session-$(date +%s)"

echo "📱 Session ID: $SESSION_ID"
echo "🔧 AutoPart ID: $AUTOPART_ID"
echo "🌐 Base URL: $BASE_URL"
echo ""

make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    
    echo "🔄 $method $endpoint"
    if [ -n "$data" ]; then
        echo "📤 Data: $data"
    fi
    
    echo "📥 Response:"
    curl -s -X "$method" "$BASE_URL$endpoint" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "X-Session-ID: $SESSION_ID" \
        ${data:+-d "$data"}
    echo ""
    echo ""
}

echo "1️⃣ Get AutoPart Details"
make_request "GET" "/auto-parts/$AUTOPART_ID"

echo "2️⃣ Add AutoPart to Wishlist"
make_request "POST" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": $AUTOPART_ID}"

echo "3️⃣ View Wishlist"
make_request "GET" "/wishlist"

echo "4️⃣ Remove from Wishlist"
make_request "DELETE" "/wishlist" "{\"buyable_type\": \"auto_part\", \"buyable_id\": $AUTOPART_ID}"

echo "✅ Test completed!"