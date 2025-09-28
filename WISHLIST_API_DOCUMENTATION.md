# 🛒 O2Mart Wishlist API Documentation

## Overview

The O2Mart Wishlist API allows users to manage their product wishlist. Users can add products, view their wishlist, and remove products. The API supports both authenticated users (via Bearer token) and guest users (via session ID).

## Authentication

The API uses the `CheckAuthOrSession` middleware which accepts either:
- **Authenticated Users**: `Authorization: Bearer {token}` header
- **Guest Users**: `X-Session-ID: {session-id}` header

## Base URL

```
https://mk3bel.o2mart.net/api
```

## Endpoints

### 1. Add Product to Wishlist

**Endpoint:** `POST /wishlist`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-Session-ID: your-session-id  (for guests)
Authorization: Bearer token    (for authenticated users)
```

**Request Body:**
```json
{
    "buyable_type": "auto_part|battery|tyre|rim",
    "buyable_id": 123
}
```

**Success Response (200):**
```json
{
    "message": "Added to wishlist",
    "session_id": "session-id-here"
}
```

**Example:**
```bash
curl -X POST "https://mk3bel.o2mart.net/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Session-ID: test-session-123" \
  -d '{"buyable_type": "auto_part", "buyable_id": 560}'
```

### 2. View Wishlist

**Endpoint:** `GET /wishlist`

**Headers:**
```
Accept: application/json
X-Session-ID: your-session-id  (for guests)
Authorization: Bearer token    (for authenticated users)
```

**Success Response (200):**
```json
{
    "message": "Wishlist loaded successfully",
    "session_id": "session-id-here",
    "wishlist": {
        "items": [
            {
                "type": "AutoPart",
                "id": 560,
                "name": "Engine Oil 1L Special G 5W40",
                "price_per_unit": 40.00,
                "quantity": 1,
                "subtotal": 40.00,
                "image": "https://example.com/image.jpg",
                "brand": {
                    "id": 1,
                    "name": "UAE",
                    "logo_url": "https://example.com/logo.jpg"
                }
            }
        ],
        "subtotal": 40.00,
        "total": 40.00
    }
}
```

**Example:**
```bash
curl -X GET "https://mk3bel.o2mart.net/api/wishlist" \
  -H "Accept: application/json" \
  -H "X-Session-ID: test-session-123"
```

### 3. Remove Product from Wishlist

**Endpoint:** `DELETE /wishlist`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-Session-ID: your-session-id  (for guests)
Authorization: Bearer token    (for authenticated users)
```

**Request Body:**
```json
{
    "buyable_type": "auto_part|battery|tyre|rim",
    "buyable_id": 123
}
```

**Success Response (200):**
```json
{
    "message": "Removed from wishlist"
}
```

**Example:**
```bash
curl -X DELETE "https://mk3bel.o2mart.net/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Session-ID: test-session-123" \
  -d '{"buyable_type": "auto_part", "buyable_id": 560}'
```

## Supported Product Types

The API supports four product types:

1. **auto_part** - Auto Parts
2. **battery** - Batteries  
3. **tyre** - Tyres
4. **rim** - Rims

## Error Responses

### 401 Unauthorized
```json
{
    "status": "error",
    "message": "Unauthenticated or session ID missing"
}
```

### 404 Not Found
```json
{
    "message": "Product not found."
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "buyable_type": ["The buyable type field is required."],
        "buyable_id": ["The buyable id field is required."]
    }
}
```

## Product Details in Response

Each product in the wishlist response includes:

### Auto Parts
- Category, Brand, Country, Viscosity Grade
- SKU, Price, Discount information
- Dimensions (length, width, height, weight)
- Description, Details, Share URL

### Batteries
- Category, Brand, Capacity, Dimension, Country
- Warranty, Weight
- Price and discount information
- Description, Share URL

### Tyres
- Brand, Model, Size, Country, Attribute
- Warranty, Dimensions, Production Year, Weight
- Price information (multiplied by 4 if set of 4)
- Description, Share URL

### Rims
- Category, Brand, Size, Country, Attribute
- SKU, Bolt Pattern, Centre Caps, Item Code
- Warranty, Weight, Offsets
- Colour, Condition, Specification
- Price information (multiplied by 4 if set of 4)
- Description, Share URL

## Implementation Details

### Models Used
- `Wishlist` - Main wishlist container
- `WishlistItem` - Individual wishlist items (polymorphic)
- `AutoPart`, `Battery`, `Tyre`, `Rim` - Product models

### Service Layer
- `WishlistService::getCurrentWishlist()` - Handles wishlist retrieval for both authenticated and guest users

### Controller
- `WishlistController` - Handles all wishlist operations
- Includes comprehensive product transformation logic
- Handles image resolution from multiple sources
- Manages brand information for all product types

## Testing

A comprehensive test script is available at `test_wishlist_demo.php` which demonstrates:
- Adding 4 different product types to wishlist
- Viewing wishlist contents with full product details
- Removing products from wishlist
- Verifying final state

Run the test with:
```bash
php test_wishlist_demo.php
```

## Session Management

- Guest users: Wishlist is tied to session ID provided in `X-Session-ID` header
- Authenticated users: Wishlist is tied to user account
- Session-based wishlists can be transferred to user account upon login

## Notes

- Products are automatically deduplicated (same product type + ID won't be added twice)
- Images are resolved from multiple possible sources with fallback logic
- Brand information includes logo URLs when available
- Prices include VAT and discount calculations
- Set of 4 products (tyres/rims) have prices automatically multiplied by 4