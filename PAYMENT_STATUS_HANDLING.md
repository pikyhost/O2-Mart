# Payment Status Handling Guide

## Backend Implementation

### 1. Clean Payment Flow
- **Webhook**: Handles actual payment status updates from Paymob
- **Redirect**: Always returns "processing" status to frontend
- **Status API**: Provides real payment status with user messages

### 2. API Endpoints

#### Check Order Status
```
GET /api/paymob/order-status/{orderId}
```

Response:
```json
{
  "status": "success|failed|not_found",
  "order_id": 123,
  "order_status": "completed|pending|payment_failed",
  "message": "Payment completed successfully! Your order has been confirmed."
}
```

#### Get Payment Messages
```
GET /api/payment/messages
```

Response:
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

## Frontend Implementation

### React Component for Payment Status Page

```jsx
import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';

const PaymentStatusPage = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [status, setStatus] = useState('processing');
  const [message, setMessage] = useState('Payment is being processed. Please wait...');
  const [orderId, setOrderId] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const urlStatus = searchParams.get('status');
    const urlOrderId = searchParams.get('order_id');
    
    setOrderId(urlOrderId);

    if (urlStatus === 'processing' && urlOrderId) {
      // Poll for actual status
      checkPaymentStatus(urlOrderId);
    } else {
      // Handle direct status from URL
      handleDirectStatus(urlStatus);
    }
  }, [searchParams]);

  const checkPaymentStatus = async (orderIdToCheck) => {
    try {
      const response = await fetch(`/api/paymob/order-status/${orderIdToCheck}`);
      const data = await response.json();
      
      setStatus(data.status);
      setMessage(data.message);
      setIsLoading(false);
      
    } catch (error) {
      console.error('Error checking payment status:', error);
      setStatus('failed');
      setMessage('Unable to verify payment status. Please contact support.');
      setIsLoading(false);
    }
  };

  const handleDirectStatus = (urlStatus) => {
    setStatus(urlStatus || 'failed');
    setMessage(getMessageForStatus(urlStatus));
    setIsLoading(false);
  };

  const getMessageForStatus = (statusType) => {
    const messages = {
      'success': 'Payment completed successfully! Your order has been confirmed.',
      'failed': 'Payment failed. Please try again or contact support.',
      'not_found': 'Order not found. Please check your order details.',
      'processing': 'Payment is being processed. Please wait...'
    };
    return messages[statusType] || 'Unknown payment status.';
  };

  const handleRetry = () => {
    navigate('/checkout');
  };

  const handleViewOrder = () => {
    if (orderId) {
      navigate(`/orders/${orderId}`);
    }
  };

  const getStatusIcon = () => {
    switch (status) {
      case 'success':
        return '✅';
      case 'failed':
        return '❌';
      case 'processing':
        return '⏳';
      default:
        return '❓';
    }
  };

  const getStatusColor = () => {
    switch (status) {
      case 'success':
        return 'text-green-600';
      case 'failed':
        return 'text-red-600';
      case 'processing':
        return 'text-blue-600';
      default:
        return 'text-gray-600';
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <div className="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div className="mb-6">
          <div className="text-6xl mb-4">{getStatusIcon()}</div>
          <h1 className={`text-2xl font-bold mb-2 ${getStatusColor()}`}>
            {status === 'success' ? 'Payment Successful' : 
             status === 'failed' ? 'Payment Failed' : 
             status === 'processing' ? 'Processing Payment' : 'Payment Status'}
          </h1>
          <p className="text-gray-600">{message}</p>
        </div>

        {orderId && (
          <div className="mb-6 p-4 bg-gray-100 rounded">
            <p className="text-sm text-gray-600">Order ID: <span className="font-mono">{orderId}</span></p>
          </div>
        )}

        <div className="space-y-3">
          {status === 'success' && orderId && (
            <button
              onClick={handleViewOrder}
              className="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors"
            >
              View Order Details
            </button>
          )}
          
          {status === 'failed' && (
            <button
              onClick={handleRetry}
              className="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition-colors"
            >
              Try Again
            </button>
          )}
          
          <button
            onClick={() => navigate('/')}
            className="w-full bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 transition-colors"
          >
            Back to Home
          </button>
        </div>

        {isLoading && status === 'processing' && (
          <div className="mt-4">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          </div>
        )}
      </div>
    </div>
  );
};

export default PaymentStatusPage;
```

## Debugging Payment Issues

### Backend Debugging

1. **Check Laravel Logs**:
```bash
tail -f storage/logs/laravel.log
```

2. **Test Webhook Manually**:
```bash
curl -X POST http://your-domain.com/api/payment/paymob/webhook \
  -H "Content-Type: application/json" \
  -d '{"success": true, "merchant_order_id": "123"}'
```

3. **Test Status API**:
```bash
curl http://your-domain.com/api/paymob/order-status/123
```

### Frontend Debugging

1. **Check Network Tab**: Monitor API calls to status endpoint
2. **Console Logs**: Add logging to track payment flow
3. **URL Parameters**: Verify status and order_id in URL

### Common Issues & Solutions

#### Issue: Frontend not calling status API
**Solution**: Check if setTimeout is working and fetch is not blocked by CORS

#### Issue: Payment shows as failed but was successful
**Solution**: Check webhook URL configuration in Paymob dashboard

#### Issue: Order status not updating
**Solution**: Verify webhook is receiving correct data format

#### Issue: User sees "processing" forever
**Solution**: Add timeout handling in frontend (max 30 seconds)

## Configuration

### Environment Variables
```env
PAYMOB_API_KEY=your_api_key
PAYMOB_INTEGRATION_ID=your_integration_id
PAYMOB_IFRAME_ID=your_iframe_id
PAYMOB_FRONTEND_REDIRECT_URL=http://localhost:3000
```

### Paymob Dashboard Settings
- **Return URL**: `http://your-domain.com/api/payment/redirect`
- **Webhook URL**: `http://your-domain.com/api/payment/paymob/webhook`

## Testing Checklist

- [ ] Successful payment flow
- [ ] Failed payment handling
- [ ] Network timeout scenarios
- [ ] Order not found cases
- [ ] Webhook processing
- [ ] Email notifications
- [ ] Shipping integration
- [ ] User feedback messages