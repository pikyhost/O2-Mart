# Frontend API Integration Guide

## Payment Flow Implementation

### 1. Payment Initiation

**Endpoint:** `POST /api/api/payment/paymob/initiate`

```javascript
const initiatePayment = async (orderId) => {
  const response = await fetch('/api/api/payment/paymob/initiate', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      order_id: orderId
    })
  });
  
  const data = await response.json();
  
  if (data.iframe_url) {
    // Open payment iframe
    window.open(data.iframe_url, '_blank');
  }
};
```

### 2. Payment Status Page

**Route:** `/payment-status`

**URL Parameters:**
- `status`: `processing|success|failed|not_found`
- `order_id`: Order ID number

```javascript
// PaymentStatusPage.jsx
import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';

const PaymentStatusPage = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [status, setStatus] = useState('processing');
  const [message, setMessage] = useState('Processing your payment...');
  const [orderId, setOrderId] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const urlStatus = searchParams.get('status');
    const urlOrderId = searchParams.get('order_id');
    
    setOrderId(urlOrderId);

    if (urlStatus === 'processing' && urlOrderId) {
      // Wait 3 seconds then check actual status
      setTimeout(() => {
        checkPaymentStatus(urlOrderId);
      }, 3000);
    } else {
      // Handle direct status
      handleDirectStatus(urlStatus);
    }
  }, [searchParams]);

  const checkPaymentStatus = async (orderIdToCheck) => {
    try {
      const response = await fetch(`/api/paymob/order-status/${orderIdToCheck}`);
      const data = await response.json();
      
      setStatus(data.status);
      setMessage(data.message);
      setLoading(false);
      
    } catch (error) {
      console.error('Payment status check failed:', error);
      setStatus('failed');
      setMessage('Payment verification failed. Please contact support.');
      setLoading(false);
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
    setLoading(false);
  };

  const getStatusIcon = () => {
    if (loading) return '⏳';
    switch (status) {
      case 'success': return '✅';
      case 'failed': return '❌';
      case 'not_found': return '❓';
      default: return '⏳';
    }
  };

  const getStatusColor = () => {
    switch (status) {
      case 'success': return 'text-green-600';
      case 'failed': return 'text-red-600';
      case 'not_found': return 'text-yellow-600';
      default: return 'text-blue-600';
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <div className="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div className="mb-6">
          <div className="text-6xl mb-4">{getStatusIcon()}</div>
          <h1 className={`text-2xl font-bold mb-2 ${getStatusColor()}`}>
            {loading ? 'Processing Payment' :
             status === 'success' ? 'Payment Successful' :
             status === 'failed' ? 'Payment Failed' :
             status === 'not_found' ? 'Order Not Found' : 'Payment Status'}
          </h1>
          <p className="text-gray-600">{message}</p>
        </div>

        {orderId && (
          <div className="mb-6 p-4 bg-gray-100 rounded">
            <p className="text-sm text-gray-600">
              Order ID: <span className="font-mono">{orderId}</span>
            </p>
          </div>
        )}

        {!loading && (
          <div className="space-y-3">
            {status === 'success' && orderId && (
              <button
                onClick={() => navigate(`/orders/${orderId}`)}
                className="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
              >
                View Order Details
              </button>
            )}
            
            {status === 'failed' && (
              <button
                onClick={() => navigate('/checkout')}
                className="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
              >
                Try Again
              </button>
            )}
            
            <button
              onClick={() => navigate('/')}
              className="w-full bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700"
            >
              Back to Home
            </button>
          </div>
        )}

        {loading && (
          <div className="mt-4">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p className="text-sm text-gray-500 mt-2">Please wait...</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default PaymentStatusPage;
```

### 3. API Endpoints Reference

#### Check Payment Status
```javascript
const checkOrderStatus = async (orderId) => {
  const response = await fetch(`/api/paymob/order-status/${orderId}`);
  return await response.json();
};

// Response format:
{
  "status": "success|failed|not_found",
  "order_id": 205,
  "order_status": "completed|pending|payment_failed",
  "message": "Payment completed successfully! Your order has been confirmed."
}
```

#### Get Payment Messages
```javascript
const getPaymentMessages = async () => {
  const response = await fetch('/api/payment/messages');
  return await response.json();
};

// Response format:
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

### 4. Router Configuration

```javascript
// App.js or Router configuration
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import PaymentStatusPage from './components/PaymentStatusPage';

function App() {
  return (
    <Router>
      <Routes>
        {/* Other routes */}
        <Route path="/payment-status" element={<PaymentStatusPage />} />
      </Routes>
    </Router>
  );
}
```

### 5. Error Handling

```javascript
const handlePaymentError = (error) => {
  console.error('Payment error:', error);
  
  // Show user-friendly error message
  setStatus('failed');
  setMessage('Something went wrong. Please try again or contact support.');
};

// Network timeout handling
const checkPaymentStatusWithTimeout = async (orderId) => {
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
  
  try {
    const response = await fetch(`/api/paymob/order-status/${orderId}`, {
      signal: controller.signal
    });
    clearTimeout(timeoutId);
    return await response.json();
  } catch (error) {
    clearTimeout(timeoutId);
    if (error.name === 'AbortError') {
      throw new Error('Request timeout');
    }
    throw error;
  }
};
```

### 6. Testing Scenarios

#### Test URLs for Development:
```
# Processing status (will poll API after 3 seconds)
http://localhost:3000/payment-status?status=processing&order_id=205

# Direct success
http://localhost:3000/payment-status?status=success&order_id=205

# Direct failure
http://localhost:3000/payment-status?status=failed&order_id=205

# Order not found
http://localhost:3000/payment-status?status=not_found
```

### 7. CSS Styling (Optional)

```css
/* PaymentStatus.css */
.payment-status-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f9fafb;
}

.status-card {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  text-align: center;
  max-width: 28rem;
  width: 100%;
}

.status-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.loading-spinner {
  animation: spin 1s linear infinite;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  width: 2rem;
  height: 2rem;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
```

### 8. Important Implementation Notes

1. **Always wait 3 seconds** before calling status API when status is "processing"
2. **Handle network errors** gracefully with user-friendly messages
3. **Provide clear action buttons** for each status type
4. **Log errors** to console for debugging
5. **Use loading states** to improve user experience
6. **Implement timeout handling** for API calls (30 seconds max)

### 9. Backend Configuration Required

Ensure your backend has this environment variable set:
```env
PAYMOB_FRONTEND_REDIRECT_URL=http://localhost:3000
# or your production frontend URL
```

This guide provides everything needed to implement the payment status page that matches your backend API exactly.