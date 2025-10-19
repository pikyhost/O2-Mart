<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Mail\OrderReceiptMail;
use App\Services\JeeblyService;
use App\Services\PaymobPaymentService;

class PaymobController extends Controller
{


    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);

        $service = new PaymobPaymentService();
        
        $paymentRequest = new Request([
            'amount_cents' => $order->total * 100,
            'contact_email' => $order->contact_email ?? $order->user?->email ?? 'guest@example.com',


            'merchant_order_id' => $order->id . '_' . time(),
            'contact_name' => $order->user?->name ?? 'Guest',
            'contact_phone' => $order->contact_phone ?? '01000000000'
        ]);
        
        $result = $service->sendPayment($paymentRequest);
        
        Log::info('Payment initiation result', ['result' => $result]);
        
        if ($result['success']) {
            return response()->json([
                'iframe_url' => $result['iframe_url']
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Failed to initiate payment',
            'debug' => $result
        ], 500);
    }
    public function handleWebhook(Request $request)
    {
        Log::info('WEBHOOK_RECEIVED', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);
        
        $data = $request->all();
        $isPaid = $data['success'] ?? false;
        $merchantOrderId = $data['merchant_order_id'] ?? null;
        
        // Extract original order ID (remove timestamp suffix)
        $orderId = $merchantOrderId ? explode('_', $merchantOrderId)[0] : null;
        
        Log::info('WEBHOOK_PROCESSING', [
            'is_paid' => $isPaid,
            'merchant_order_id' => $merchantOrderId,
            'extracted_order_id' => $orderId
        ]);

        if ($isPaid && $orderId) {
            $order = Order::with('user', 'items')->find($orderId);
            
            Log::info('ORDER_FOUND', [
                'order_id' => $orderId,
                'order_exists' => $order ? true : false,
                'current_status' => $order?->status
            ]);

            if ($order && $order->status !== 'paid') {
                $order->update(['status' => 'paid']);
                
                Log::info('ORDER_UPDATED', [
                    'order_id' => $order->id,
                    'new_status' => 'paid'
                ]);

                try {
                    if (!$order->tracking_number) {
                        $shippingService = new JeeblyService();
                        $shippingService->createShipment($order);
                    }

                    $email = $order->contact_email ?? $order->user?->email;
                    if ($email) {
                        Mail::to($email)->send(new OrderReceiptMail($order));
                    }
                } catch (\Exception $e) {
                    Log::error('Webhook processing failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } else {
            Log::warning('WEBHOOK_IGNORED', [
                'reason' => !$isPaid ? 'payment_not_successful' : 'no_order_id',
                'is_paid' => $isPaid,
                'order_id' => $orderId
            ]);
        }

        return response()->json(['status' => 'ok']);
    }



    public function handleRedirect(Request $request)
    {
        Log::info('REDIRECT_RECEIVED', ['request' => $request->all()]);
        
        $merchantOrderId = $request->query('merchant_order_id');
        $success = $request->query('success');
        
        // Extract original order ID (remove timestamp suffix)
        $orderId = $merchantOrderId ? explode('_', $merchantOrderId)[0] : null;
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=not_found');
        }
        
        // Update order status if payment was successful (workaround for missing webhook)
        if ($success === 'true' && $order->status === 'pending') {
            $order->update(['status' => 'paid']);
            Log::info('ORDER_UPDATED_VIA_REDIRECT', ['order_id' => $order->id]);
            
            // Create Jeebly shipment and send email
            try {
                if (!$order->tracking_number) {
                    $shippingService = new JeeblyService();
                    $shippingService->createShipment($order);
                    Log::info('JEEBLY_SHIPMENT_CREATED_VIA_REDIRECT', ['order_id' => $order->id]);
                }

                $email = $order->contact_email ?? $order->user?->email;
                if ($email) {
                    Mail::to($email)->send(new OrderReceiptMail($order));
                    Log::info('EMAIL_SENT_VIA_REDIRECT', ['order_id' => $order->id]);
                }
            } catch (\Exception $e) {
                Log::error('REDIRECT_PROCESSING_FAILED', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Always redirect to processing, frontend will check status via API
        return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=processing&order_id=' . $order->id);
    }

    
    public function checkOrderStatus($orderId)
    {
        try {
            Log::info('ORDER_STATUS_CHECK', ['order_id' => $orderId]);
            
            $order = Order::find($orderId);
            
            if (!$order) {
                Log::warning('ORDER_NOT_FOUND', ['order_id' => $orderId]);
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Order not found. Please check your order details.'
                ], 200); // Changed to 200 to avoid fetch errors
            }
            
            // Wait for webhook to process if order is still pending
            $maxAttempts = 5;
            $attempt = 0;
            
            while ($attempt < $maxAttempts && $order->status === 'pending') {
                Log::info('WAITING_FOR_WEBHOOK', ['attempt' => $attempt + 1, 'order_id' => $orderId]);
                sleep(1);
                $order->refresh();
                $attempt++;
            }
            
            $status = $order->status === 'paid' ? 'success' : 'failed';
            $message = $this->getPaymentMessage($status);
            
            Log::info('ORDER_STATUS_RESPONSE', [
                'order_id' => $orderId,
                'order_status' => $order->status,
                'response_status' => $status,
                'message' => $message
            ]);
            
            return response()->json([
                'status' => $status,
                'order_id' => (int) $order->id,
                'order_status' => $order->status,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('ORDER_STATUS_CHECK_ERROR', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to check order status. Please contact support.',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function getPaymentMessage($status)
    {
        return match($status) {
            'success' => 'Thank You! Your order has been successfully placed. We\'ll get back to you shortly.',
            'failed' => 'Payment failed. Please try again or contact support.',
            'not_found' => 'Order not found. Please check your order details.',
            default => 'Payment is being processed. Please wait...'
        };
    }

    public function getPaymentMessages()
    {
        return response()->json([
            'messages' => [
                'success' => 'Payment completed successfully! Your order has been confirmed.',
                'failed' => 'Payment failed. Please try again or contact support.',
                'processing' => 'Payment is being processed. Please wait...',
                'not_found' => 'Order not found. Please check your order details.',
                'timeout' => 'Payment verification timed out. Please contact support if you were charged.'
            ]
        ]);
    }
}
