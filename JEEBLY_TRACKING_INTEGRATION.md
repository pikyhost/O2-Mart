# Jeebly Tracking Integration

This document explains the Jeebly tracking integration that automatically syncs order statuses with Jeebly's tracking system.

## Features

- **Automatic Status Sync**: Orders with Jeebly tracking numbers are automatically synced every 10 minutes
- **Manual Sync**: Ability to manually sync specific orders or batches
- **Webhook Support**: Endpoint for Jeebly to push status updates (if supported)
- **Status Mapping**: Jeebly statuses are mapped to internal order and shipping statuses

## Configuration

Add these environment variables to your `.env` file:

```env
JEEBLY_BASE_URL=https://demo.jeebly.com
JEEBLY_API_KEY=your_api_key_here
JEEBLY_CLIENT_KEY=your_client_key_here
```

## Usage

### Automatic Sync (Scheduled)

The system automatically syncs order statuses every 10 minutes via Laravel's task scheduler:

```bash
# Make sure your cron job is running
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Manual Sync Commands

```bash
# Sync all pending orders (limit 50)
php artisan jeebly:sync-status

# Sync specific order
php artisan jeebly:sync-status --order-id=123

# Sync with custom limit
php artisan jeebly:sync-status --limit=100
```

### Webhook Endpoint

If Jeebly supports webhooks, configure this endpoint:

```
POST /api/webhooks/jeebly/status-update
```

Expected payload:
```json
{
    "reference_number": "JB304162",
    "status": "delivered"
}
```

## Status Mapping

### Jeebly Status → Order Status

| Jeebly Status | Order Status |
|---------------|--------------|
| delivered | completed |
| cancelled | cancelled |
| rto_delivered | refund |
| * (others) | shipping |

### Jeebly Status → Shipping Status

| Jeebly Status | Shipping Status |
|---------------|-----------------|
| Pickup Scheduled | pickup_scheduled |
| Pickup Completed | pickup_completed |
| Inscan At Hub | at_hub |
| Reached At Hub | at_hub |
| Out For Delivery | out_for_delivery |
| Delivered | delivered |
| Undelivered | delivery_failed |
| On-Hold | on_hold |
| RTO | returning |
| RTO Delivered | returned |
| Cancelled | cancelled |

## Testing

Use the test script to verify tracking functionality:

```bash
php test_jeebly_tracking.php
```

## Files Modified/Created

### New Files
- `app/Console/Commands/SyncJeeblyOrderStatus.php` - Manual sync command
- `app/Http/Controllers/JeeblyWebhookController.php` - Webhook handler
- `test_jeebly_tracking.php` - Test script

### Modified Files
- `app/Services/JeeblyService.php` - Added tracking and status mapping methods
- `app/Jobs/UpdateShipmentStatus.php` - Updated to use new service methods
- `app/Console/Kernel.php` - Updated scheduled task
- `routes/api.php` - Added webhook route

## Monitoring

Check logs for tracking updates:

```bash
tail -f storage/logs/laravel.log | grep -i jeebly
```

## Troubleshooting

1. **Orders not syncing**: Check if tracking numbers exist and are valid
2. **API errors**: Verify API credentials in `.env` file
3. **Status not updating**: Check if order status allows updates (not completed/cancelled)
4. **Queue issues**: Ensure queue workers are running for background jobs