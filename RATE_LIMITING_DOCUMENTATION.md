# API Rate Limiting Documentation - O2Mart

## Overview

Rate limiting has been implemented across all API endpoints to protect against abuse, prevent DDoS attacks, and ensure fair resource allocation for all users.

---

## Rate Limit Configurations

### 1. **Default API Rate Limiter**
- **Limit:** 60 requests per minute per IP
- **Applied to:** All API routes by default
- **Identifier:** IP address
- **Use case:** General API protection

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});
```

---

### 2. **Authenticated API Rate Limiter**
- **Limit:** 120 requests per minute for authenticated users, 60 for guests
- **Applied to:** Authenticated routes
- **Identifier:** User ID (if authenticated), IP address (if guest)
- **Use case:** Higher limits for logged-in users

```php
RateLimiter::for('api-authenticated', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(120)->by($request->user()->id)
        : Limit::perMinute(60)->by($request->ip());
});
```

---

### 3. **Authentication Endpoints** (`throttle.auth`)
- **Limit:** 5 requests per minute per IP
- **Applied to:**
  - `/api/forgot-password`
  - `/api/reset-password`
  - `/api/password/validate`
- **Response:** Custom 429 error with message
- **Use case:** Prevent brute force attacks on authentication

```php
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip())
        ->response(function (Request $request, array $headers) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many attempts. Please try again later.',
            ], 429, $headers);
        });
});
```

---

### 4. **Form Submissions** (`throttle.forms`)
- **Limit:** 10 requests per minute per IP
- **Applied to:**
  - `/api/contact` (Contact form)
  - `/api/newsletter/subscribe` (Newsletter subscription)
  - `/api/suppliers` (Supplier registration)
  - `/api/inquiries` (Product inquiries)
- **Response:** Custom 429 error
- **Use case:** Prevent spam submissions

```php
RateLimiter::for('forms', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip())
        ->response(function (Request $request, array $headers) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many form submissions. Please try again later.',
            ], 429, $headers);
        });
});
```

---

### 5. **Checkout Operations** (`throttle.checkout`)
- **Limit:** 10 requests per minute for authenticated users, 5 for guests
- **Applied to:**
  - `/api/checkout` (User checkout)
  - `/api/checkout/guest` (Guest checkout)
- **Identifier:** User ID (if authenticated), IP address (if guest)
- **Use case:** Prevent checkout abuse

```php
RateLimiter::for('checkout', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(10)->by($request->user()->id)
        : Limit::perMinute(5)->by($request->ip());
});
```

---

### 6. **Search Operations** (`throttle.search`)
- **Limit:** 30 requests per minute per IP
- **Applied to:**
  - `/api/search` (General search/dropdown endpoint)
- **Identifier:** IP address
- **Use case:** Prevent search abuse while allowing reasonable usage

```php
RateLimiter::for('search', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});
```

---

### 7. **Cart Operations** (`throttle.cart`)
- **Limit:** 60 requests per minute
- **Applied to:**
  - `/api/cart`
  - `/api/cart-menu`
  - `/api/cart/add`
  - `/api/cart/add-tyre-group`
  - `/api/cart/remove`
  - `/api/cart/update-quantity`
  - `/api/cart/calculate-shipping`
  - `/api/cart/summary`
  - All cart-related endpoints
- **Identifier:** User ID (if authenticated), IP address (if guest)
- **Use case:** Allow frequent cart operations while preventing abuse

```php
RateLimiter::for('cart', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
});
```

---

## HTTP Response Headers

When rate limiting is active, the following headers are included in responses:

```
X-RateLimit-Limit: 60         # Maximum requests allowed
X-RateLimit-Remaining: 45     # Requests remaining in current window
X-RateLimit-Reset: 1730000000 # Unix timestamp when limit resets
```

---

## Error Response Format

When rate limit is exceeded (429 status):

### Default Response:
```json
{
  "message": "Too Many Attempts."
}
```

### Custom Responses (auth & forms):
```json
{
  "status": "error",
  "message": "Too many attempts. Please try again later."
}
```

```json
{
  "status": "error",
  "message": "Too many form submissions. Please try again later."
}
```

---

## Implementation Details

### Configuration Location

**File:** `app/Providers/AppServiceProvider.php`

All rate limiters are configured in the `configureRateLimiting()` method.

### Middleware Registration

**File:** `bootstrap/app.php`

Middleware aliases are registered:
```php
$middleware->alias([
    'throttle.auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':auth',
    'throttle.forms' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':forms',
    'throttle.checkout' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':checkout',
    'throttle.search' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':search',
    'throttle.cart' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':cart',
]);
```

### Route Application

**File:** `routes/api.php`

Applied to specific route groups:
```php
// Authentication endpoints
Route::middleware('throttle.auth')->group(function () {
    Route::post('/forgot-password', ...);
    Route::post('/reset-password', ...);
});

// Form submissions
Route::middleware('throttle.forms')->group(function () {
    Route::post('/contact', ...);
    Route::post('/inquiries', ...);
});

// Cart operations
Route::middleware([CheckAuthOrSession::class, 'throttle.cart'])->group(function () {
    Route::get('/cart', ...);
    Route::post('/cart/add', ...);
});

// Checkout
Route::middleware('throttle.checkout')->group(function () {
    Route::post('/checkout/guest', ...);
});
```

---

## Protected Endpoints Summary

| Endpoint Category | Rate Limiter | Limit (per minute) | Identifier |
|-------------------|--------------|-------------------|------------|
| Default API | `api` | 60 | IP |
| Authenticated API | `api-authenticated` | 120 (auth) / 60 (guest) | User ID / IP |
| Password Reset | `throttle.auth` | 5 | IP |
| Contact Forms | `throttle.forms` | 10 | IP |
| Newsletter | `throttle.forms` | 10 | IP |
| Supplier Form | `throttle.forms` | 10 | IP |
| Inquiries | `throttle.forms` | 10 | IP |
| Search/Dropdowns | `throttle.search` | 30 | IP |
| Cart Operations | `throttle.cart` | 60 | User ID / IP |
| Checkout | `throttle.checkout` | 10 (auth) / 5 (guest) | User ID / IP |

---

## Testing Rate Limits

### Test with curl:

```bash
# Test auth rate limit (5 requests/minute)
for i in {1..6}; do
  curl -X POST https://api.o2mart.net/api/forgot-password \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com"}'
  sleep 1
done

# Test cart rate limit (60 requests/minute)
for i in {1..65}; do
  curl https://api.o2mart.net/api/cart \
    -H "Cookie: laravel_session=your-session"
  sleep 0.5
done
```

### Check Response Headers:

```bash
curl -I https://api.o2mart.net/api/cart
```

Look for:
- `X-RateLimit-Limit`
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset`

---

## Adjusting Rate Limits

To modify rate limits, edit `app/Providers/AppServiceProvider.php`:

```php
// Example: Increase auth limit to 10 requests/minute
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});

// Example: Add hourly limit
RateLimiter::for('auth', function (Request $request) {
    return [
        Limit::perMinute(5)->by($request->ip()),
        Limit::perHour(20)->by($request->ip()),
    ];
});
```

---

## Cache Driver

Rate limiting uses the default cache driver configured in `config/cache.php`.

**Recommended for production:** Redis or Memcached for better performance.

```env
CACHE_DRIVER=redis
```

---

## Monitoring & Logs

### Log Rate Limit Hits:

Add to middleware if needed:
```php
->response(function (Request $request, array $headers) {
    \Log::warning('Rate limit exceeded', [
        'ip' => $request->ip(),
        'endpoint' => $request->path(),
        'user_id' => $request->user()?->id,
    ]);
    
    return response()->json([
        'status' => 'error',
        'message' => 'Too many attempts. Please try again later.',
    ], 429, $headers);
});
```

---

## Best Practices

1. **Monitor rate limit hits** in production logs
2. **Adjust limits** based on actual usage patterns
3. **Use Redis** for cache driver in production
4. **Communicate limits** to API consumers via documentation
5. **Implement retry logic** in frontend with exponential backoff
6. **Whitelist** critical IPs if needed (e.g., monitoring services)

---

## Frontend Integration

### Handle 429 Responses:

```javascript
// Example: Axios interceptor
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 429) {
      const retryAfter = error.response.headers['retry-after'] || 60;
      
      // Show user-friendly message
      toast.error(`Too many requests. Please wait ${retryAfter} seconds.`);
      
      // Optional: Implement retry with exponential backoff
      return new Promise(resolve => {
        setTimeout(() => {
          resolve(axios(error.config));
        }, retryAfter * 1000);
      });
    }
    return Promise.reject(error);
  }
);
```

---

## Security Benefits

✅ **Prevents brute force attacks** on authentication endpoints  
✅ **Mitigates DDoS attacks** by limiting request rates  
✅ **Reduces spam** on form submissions  
✅ **Protects server resources** from abuse  
✅ **Ensures fair usage** for all users  
✅ **Improves API stability** and performance

---

## Support

For rate limit adjustments or issues, contact the development team or check application logs at `storage/logs/laravel.log`.
