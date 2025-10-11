# Paymob Payment Status Implementation Guide

## Overview
This guide explains how to implement the payment status flow for Paymob payments. The backend always redirects to a processing state, and the frontend must poll the API to get the final payment status.

## Payment Flow

1. **Backend Redirect**: After payment, backend redirects to: `/payment-status?status=processing&order_id=123`
2. **Frontend Processing**: Show processing spinner for 3 seconds
3. **Status Check**: Call API to get actual payment status
4. **Display Result**: Show success or failed based on API response

## API Endpoint

### Check Order Status
```
GET /api/paymob/order-status/{orderId}
```

**Example Request:**
```bash
curl -X GET "https://o2mart.to7fa.online/api/paymob/order-status/248" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
  "status": "success",
  "order_id": 248,
  "order_status": "completed",
  "message": "Thank You! Your order has been successfully placed. We'll get back to you shortly."
}
```

**Failed Response (200):**
```json
{
  "status": "failed",
  "order_id": 248,
  "order_status": "pending",
  "message": "Payment failed. Please try again or contact support."
}
```

**Not Found Response (200):**
```json
{
  "status": "not_found",
  "message": "Order not found. Please check your order details."
}
```

## Frontend Implementation

### URL Parameters
- `status=processing` - Always sent by backend
- `order_id=123` - The order ID to check

### Implementation Steps

1. **Parse URL Parameters**
   ```javascript
   const status = searchParams.get("status");
   const orderId = searchParams.get("order_id");
   ```

2. **Show Processing State**
   ```javascript
   if (status === "processing" && orderId) {
     setCurrentState("processing");
     // Show spinner for 3 seconds
   }
   ```

3. **Call API After 3 Seconds**
   ```javascript
   setTimeout(() => {
     fetch(`https://o2mart.to7fa.online/api/paymob/order-status/${orderId}`)
       .then(res => res.json())
       .then(data => {
         if (data.status === "success") {
           setCurrentState("success");
         } else {
           setCurrentState("failed");
         }
         setMessage(data.message);
       });
   }, 3000);
   ```

## Status Values

| Status | Description | Action |
|--------|-------------|--------|
| `success` | Payment completed successfully | Show success page |
| `failed` | Payment failed or pending | Show failed page |
| `not_found` | Order doesn't exist | Show error page |

## Example Postman Collection

### Request
- **Method**: GET
- **URL**: `https://o2mart.to7fa.online/api/paymob/order-status/248`
- **Headers**: 
  - `Accept: application/json`

### Test Cases

1. **Successful Payment**
   - Order ID: 248 (completed order)
   - Expected: `status: "success"`

2. **Failed Payment**
   - Order ID: 247 (pending order)
   - Expected: `status: "failed"`

3. **Invalid Order**
   - Order ID: 99999 (non-existent)
   - Expected: `status: "not_found"`

## Important Notes

- **Always wait 3 seconds** before calling the API to allow webhook processing
- **Backend always returns processing** initially to prevent payment timing issues
- **Use the message field** from API response for user-friendly messages
- **Handle all status types** (success, failed, not_found) in your frontend
- **API returns 200 status** for all responses, check the `status` field in JSON

## Error Handling

```javascript
.catch(error => {
  console.error("API Error:", error);
  setCurrentState("failed");
  setMessage("Could not verify payment status. Please contact support.");
});
```

This implementation ensures a smooth payment experience by handling the timing between Paymob redirect and webhook processing.