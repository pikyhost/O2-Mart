# Wishlist API Fixes - Battery Attributes

## Issues Fixed

### 1. Image Resolution
- **Problem**: AutoPart and Battery images were not displaying correctly in wishlist
- **Solution**: Updated `resolveImage()` method to use correct model methods:
  - AutoPart: `getAutoPartFeatureImageUrl()`
  - Battery: `feature_image_url` attribute

### 2. Battery Attributes Alignment
- **Problem**: Wishlist battery items didn't match `/api/batteries` endpoint attributes
- **Solution**: Added all battery-specific attributes to match the batteries API:

#### Added Battery Attributes:
- `warranty` - Battery warranty period
- `capacity` - Battery capacity object with id and value
- `dimension` - Battery dimension object with id and value  
- `weight` - Battery weight
- `sku` - Battery SKU
- `regular_price` - Original price
- `discount_percentage` - Discount percentage
- `discounted_price` - Final discounted price

#### Fixed Battery Brand Format:
- Changed from `name` to `value` to match batteries API
- Maintained `id` and `logo_url` fields

### 3. Relationship Loading
- **Problem**: Trying to load all relationships on all product types caused errors
- **Solution**: Conditional relationship loading based on buyable type:
  - Battery: `batteryBrand`, `capacity`, `dimension`, `batteryCountry`
  - AutoPart: `autoPartBrand`, `autoPartCountry`
  - Tyre: `tyreBrand`, `tyreCountry`
  - Rim: `rimBrand`, `rimCountry`

## Result
Wishlist now returns identical battery attributes as `/api/batteries` endpoint for seamless frontend integration.

## Test Commands
```bash
# Add battery to wishlist
curl -X POST "http://localhost:8000/api/wishlist" \
  -H "Content-Type: application/json" \
  -H "X-Session-ID: test-session" \
  -d '{"buyable_type": "battery", "buyable_id": 235}'

# View wishlist with battery attributes
curl -X GET "http://localhost:8000/api/wishlist" \
  -H "Accept: application/json" \
  -H "X-Session-ID: test-session"
```