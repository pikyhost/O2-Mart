# Payment Integration Documentation for Frontend

## Payment Flow Overview

1. **Checkout** → Create order and get Paymob iframe URL
2. **Payment** → User pays in Paymob iframe
3. **Redirect** → Paymob redirects to frontend with `status=processing`
4. **Status Check** → Frontend polls API after 3 seconds for real status

## API Endpoints

### 1. Initiate Payment
```
POST /api/api/payment/paymob/initiate
```

**Request:**
```json
{
  "order_id": 205
}
```

**Response:**
```json
{
  "iframe_url": "https://uae.paymob.com/api/acceptance/iframes/26092?payment_token=..."
}
```

### 2. Payment Redirect (Automatic)
```
GET /api/payment/redirect?merchant_order_id=205
```

**Redirects to:**
```
https://your-frontend.com/payment-status?status=processing&order_id=205
```

### 3. Check Payment Status
```
GET /api/paymob/order-status/{orderId}
```

**Response:**
```json
{
  "status": "success|failed|not_found",
  "order_id": 205,
  "order_status": "completed|pending|payment_failed",
  "message": "Payment completed successfully! Your order has been confirmed."
}
```

### 4. Get Payment Messages
```
GET /api/payment/messages
```

**Response:**
```json
{
  "messages": {
    "success": "Payment completed successfully! Your order has been confirmed.",
    "failed": "Payment failed. Please try again or contact support.",
    "processing": "Payment is being processed. Please wait...",
    "not_found": "Order not found. Please check your order details.",
    "timeout": "Payment verification timed out. Please contact support if you were charged."
  }
}
```

## Frontend Implementation Requirements

### 1. Payment Status Page Route
Create route: `/payment-status`

### 2. URL Parameters Handling
- `status`: `processing|success|failed|not_found`
- `order_id`: Order ID number

### 3. Status Polling Logic
```javascript
// When status=processing, wait 3 seconds then poll
if (status === 'processing' && orderId) {
  setTimeout(() => {
    fetch(`/api/paymob/order-status/${orderId}`)
      .then(res => res.json())
      .then(data => {
        setStatus(data.status);
        setMessage(data.message);
      });
  }, 3000);
}
```

### 4. Status Display
- **Success**: Green checkmark, "View Order" button
- **Failed**: Red X, "Try Again" button  
- **Processing**: Loading spinner, "Please wait..." message
- **Not Found**: Warning icon, "Back to Home" button

### 5. Error Handling
- Network errors → Show "failed" status
- Timeout after 30 seconds → Show timeout message
- Invalid responses → Show generic error

## React Component Example

```jsx
import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';

const PaymentStatusPage = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [status, setStatus] = useState('processing');
  const [message, setMessage] = useState('Processing your payment...');
  const [orderId, setOrderId] = useState(null);

  useEffect(() => {
    const urlStatus = searchParams.get('status');
    const urlOrderId = searchParams.get('order_id');
    
    setOrderId(urlOrderId);

    if (urlStatus === 'processing' && urlOrderId) {
      // Wait 3 seconds then check status
      setTimeout(() => {
        checkPaymentStatus(urlOrderId);
      }, 3000);
    } else {
      handleDirectStatus(urlStatus);
    }
  }, [searchParams]);

  const checkPaymentStatus = async (orderIdToCheck) => {
    try {
      const response = await fetch(`/api/paymob/order-status/${orderIdToCheck}`);
      const data = await response.json();
      
      setStatus(data.status);
      setMessage(data.message);
      
    } catch (error) {
      setStatus('failed');
      setMessage('Payment verification failed. Please contact support.');
    }
  };

  const handleDirectStatus = (urlStatus) => {
    const messages = {
      'success': 'Payment completed successfully!',
      'failed': 'Payment failed. Please try again.',
      'not_found': 'Order not found.',
    };
    
    setStatus(urlStatus || 'failed');
    setMessage(messages[urlStatus] || 'Payment failed.');
  };

  return (
    <div className="payment-status-container">
      <div className="status-card">
        <div className="status-icon">
          {status === 'success' ? '✅' : 
           status === 'failed' ? '❌' : '⏳'}
        </div>
        
        <h1 className={`status-title ${status}`}>
          {status === 'success' ? 'Payment Successful' : 
           status === 'failed' ? 'Payment Failed' : 'Processing Payment'}
        </h1>
        
        <p className="status-message">{message}</p>
        
        {orderId && (
          <p className="order-id">Order ID: {orderId}</p>
        )}
        
        <div className="action-buttons">
          {status === 'success' && (
            <button onClick={() => navigate(`/orders/${orderId}`)}>
              View Order
            </button>
          )}
          
          {status === 'failed' && (
            <button onClick={() => navigate('/checkout')}>
              Try Again
            </button>
          )}
          
          <button onClick={() => navigate('/')}>
            Back to Home
          </button>
        </div>
      </div>
    </div>
  );
};

export default PaymentStatusPage;
```

## CSS Styling

```css
.payment-status-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
}

.status-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  text-align: center;
  max-width: 400px;
}

.status-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.status-title.success { color: #10b981; }
.status-title.failed { color: #ef4444; }
.status-title.processing { color: #3b82f6; }

.action-buttons {
  margin-top: 1.5rem;
}

.action-buttons button {
  margin: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: #3b82f6;
  color: white;
}

.action-buttons button:hover {
  background: #2563eb;
}
```

## Route Setup

```jsx
// In your router configuration
import PaymentStatusPage from './components/PaymentStatusPage';

<Route path="/payment-status" element={<PaymentStatusPage />} />
```

## Testing Scenarios

### 1. Successful Payment
1. Complete payment in iframe
2. Redirect to `/payment-status?status=processing&order_id=X`
3. After 3 seconds, API returns `status: "success"`
4. Show success message with "View Order" button

### 2. Failed Payment
1. Cancel/fail payment in iframe
2. Redirect to `/payment-status?status=processing&order_id=X`
3. After 3 seconds, API returns `status: "failed"`
4. Show error message with "Try Again" button

### 3. Network Issues
1. API call fails or times out
2. Show generic error message
3. Provide "Contact Support" option

## Error Messages

Use consistent messages:

- **Success**: "Payment completed successfully! Your order has been confirmed."
- **Failed**: "Payment failed. Please try again or contact support."
- **Processing**: "Payment is being processed. Please wait..."
- **Not Found**: "Order not found. Please check your order details."
- **Timeout**: "Payment verification timed out. Please contact support if you were charged."

## Important Notes

1. **Always wait 3 seconds** before polling status API
2. **Don't show "failed"** immediately on redirect - always poll first
3. **Handle network errors** gracefully
4. **Provide clear user actions** for each status
5. **Log frontend errors** for debugging

## Configuration Required

Set your frontend URL in backend environment:
```env
PAYMOB_FRONTEND_REDIRECT_URL=https://your-frontend-domain.com
```

This implementation ensures reliable payment status handling with proper user feedback.