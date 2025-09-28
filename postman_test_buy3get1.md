# Postman Test: Buy 3 Get 1 Free - Production

## Test Sequence for Tyre ID: 8014

### 1. Add 4 Tyres to Cart
```
POST https://o2mart.to7fa.online/api/cart/add
Headers:
- Content-Type: application/json
- X-Session-ID: test-buy3get1-session

Body:
{
    "buyable_type": "tyre",
    "buyable_id": 8014,
    "quantity": 4
}
```

### 2. Get Cart Summary
```
GET https://o2mart.to7fa.online/api/cart/summary
Headers:
- Content-Type: application/json
- X-Session-ID: test-buy3get1-session
```

### 3. Get Cart Details
```
GET https://o2mart.to7fa.online/api/cart
Headers:
- Content-Type: application/json
- X-Session-ID: test-buy3get1-session
```

### 4. Check Tyre Details (Optional)
```
GET https://o2mart.to7fa.online/api/tyres/8014
Headers:
- Content-Type: application/json
```

## Expected Results:
- **Quantity**: 4 tyres
- **Price per unit**: 469.43 AED (or actual tyre price)
- **Paid quantity**: 3 (due to buy 3 get 1 free)
- **Subtotal**: Price × 3 = Expected subtotal
- **Both cart and cart summary should show same subtotal**

## Test Steps:
1. Use same X-Session-ID for all requests
2. First add tyres to cart
3. Check both cart summary and cart details
4. Verify subtotals match
5. Verify quantity shows 4 but payment is for 3

## Clean Up (Optional):
```
DELETE https://o2mart.to7fa.online/api/cart/remove
Headers:
- Content-Type: application/json
- X-Session-ID: test-buy3get1-session

Body:
{
    "buyable_type": "tyre",
    "buyable_id": 8014
}
```