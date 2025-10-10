# Payment Integration Documentation

## الوصف بالعربي - لماذا نحتاج هذا التعديل؟

### المشكلة الحالية:
عندما يدفع المستخدم عبر Paymob ويكمل الدفع بنجاح، يحدث التالي:
1. المستخدم يكمل الدفع بنجاح
2. Paymob يعيد توجيهه فوراً لموقعنا
3. **لكن** الـ webhook (الإشعار) من Paymob يصل متأخراً بـ 2-5 ثوانٍ
4. لذلك الموقع يظهر "فشل الدفع" لثوانٍ قليلة
5. ثم فجأة يتغير إلى "نجح الدفع"

### لماذا يحدث هذا؟
- Paymob يرسل المستخدم لموقعنا **قبل** أن يرسل إشعار تأكيد الدفع
- الباك إند لا يعرف حالة الدفع الحقيقية عند وصول المستخدم
- فيظهر "فشل" مؤقتاً حتى يصل الإشعار

### تأثير المشكلة:
- المستخدم يخاف أن دفعته فشلت
- تجربة مستخدم سيئة جداً
- قد يحاول الدفع مرة أخرى

### الحل:
- الباك إند سيرسل دائماً `status=processing` (معالجة)
- الفرونت إند يظهر "جاري المعالجة" لمدة 3 ثوانٍ
- ثم يتحقق من حالة الدفع الحقيقية عبر API
- **لن يظهر فشل أبداً إلا إذا كان فشل حقيقي**

---

## Problem (English)
Paymob payment flow causes a brief "failed" page flash before showing success, creating poor UX.

## Solution
Backend always returns `status=processing` and frontend polls for actual status after 3 seconds.

## What You Need to Implement

### 1. Create Payment Status Page
Create a page/route at: `/payment-status`

### 2. Handle URL Parameters
The page will receive these parameters:
- `status=processing` → Show loading, then check API after 3 seconds
- `status=not_found` → Show "Order not found" error
- `order_id=123` → The order ID to check status for

### 3. API Endpoint Available
**GET** `/api/paymob/order-status/{orderId}`

**Response:**
```json
{
  "status": "success|failed",
  "order_id": 123,
  "order_status": "completed|pending"
}
```

### 4. Implementation Logic
```javascript
// Get URL parameters
const status = // get 'status' from URL
const orderId = // get 'order_id' from URL

if (status === 'processing' && orderId) {
  // Show loading spinner immediately
  showLoadingState();
  
  // Wait exactly 3 seconds, then check API
  setTimeout(() => {
    fetch(`/api/paymob/order-status/${orderId}`)
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showSuccessPage(data.order_id);
        } else {
          showFailedPage();
        }
      })
      .catch(() => showFailedPage());
  }, 3000); // MUST be 3000ms
}
```

### 5. Required UI States
- **Loading State**: Spinner + "Processing payment..." (for 3 seconds)
- **Success State**: ✅ + "Payment successful!" + order details
- **Failed State**: ❌ + "Payment failed" + retry button
- **Not Found State**: ⚠️ + "Order not found"

### 6. User Journey
1. User completes payment on Paymob
2. Gets redirected to: `/payment-status?status=processing&order_id=123`
3. Sees loading spinner for exactly 3 seconds
4. API is called and final result is shown

## Critical Requirements
- **NEVER** show failed status immediately
- **ALWAYS** wait exactly 3 seconds before calling API
- Handle network errors by showing failed state
- Show clear loading indicators during the 3-second wait

## Testing
Test payments using: `/api/paymob/test-payment`