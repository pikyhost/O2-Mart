# O2Mart Wishlist API - Curl Commands

## Test AutoPart Wishlist with Images

### 1. Add AutoPart to Wishlist
```bash
curl -X POST "http://localhost:8000/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"buyable_type": "auto_part", "buyable_id": 6}'
```

### 2. View Wishlist (with Images)
```bash
curl -X GET "http://localhost:8000/api/wishlist" \
  -H "Accept: application/json"
```

### 3. Add Battery to Wishlist
```bash
curl -X POST "http://localhost:8000/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"buyable_type": "battery", "buyable_id": 235}'
```

### 4. Remove AutoPart from Wishlist
```bash
curl -X DELETE "http://localhost:8000/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"buyable_type": "auto_part", "buyable_id": 6}'
```

## Available Product IDs for Testing

### AutoParts (with images):
- ID: 6 - Engine Oil 1L Special G 5W40
- ID: 7 - Acdelco Engine Oil 5W-20 1L
- ID: 8 - Mopar Max pro Engine Oil 5W-20 1L

### Batteries (with images):
- ID: 235 - Amaron - 85D23L 12V 60AH JIS Car Battery
- ID: 272 - Varta 12V DIN 105AH AGM Car Battery

## Expected Response Format

When viewing wishlist, you'll get:
```json
{
  "message": "Wishlist loaded successfully",
  "session_id": "session-id-here",
  "wishlist": {
    "items": [
      {
        "type": "AutoPart",
        "id": 6,
        "name": "Engine Oil 1L Special G 5W40",
        "price_per_unit": 40,
        "quantity": 1,
        "subtotal": 40,
        "image": "http://localhost:8000/storage/image-url-here",
        "brand": {
          "id": 2,
          "name": "UAE",
          "logo_url": ""
        },
        "country": "O2--SL-00072",
        "size": "1 (kg)"
      }
    ],
    "subtotal": 40,
    "total": 40
  }
}
```

## Notes
- Images are included in the `image` field of each wishlist item
- Session-based wishlist works without authentication
- Supports AutoPart, Battery, Tyre, and Rim product types
- Duplicate items are automatically handled