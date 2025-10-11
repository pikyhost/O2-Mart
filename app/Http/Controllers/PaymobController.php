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
        $paymentToken = $service->generatePaymentToken($order);

        $iframeId = config('services.paymob.iframe_id');
        $iframeUrl = "https://accept.paymob.com/api/acceptance/iframe/{$iframeId}?payment_token={$paymentToken}";

        return response()->json([
            'iframe_url' => $iframeUrl
        ]);
    }
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        $isPaid = $data['success'] ?? false;
        $orderId = $data['merchant_order_id'] ?? null;

        if ($isPaid && $orderId) {
            $order = Order::with('user', 'items')->find($orderId);

            if ($order && $order->status !== 'completed') {
                $order->update(['status' => 'completed']);

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
        }

        return response()->json(['status' => 'ok']);
    }



    public function handleRedirect(Request $request)
    {
        $merchantOrderId = $request->query('merchant_order_id');
        $order = Order::find($merchantOrderId);

        if (!$order) {
            return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=not_found');
        }

        // Always redirect to processing, frontend will check status via API
        return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=processing&order_id=' . $order->id);
    }

    
    public function checkOrderStatus($orderId)
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        // Wait for webhook to process if order is still pending
        $maxAttempts = 5;
        $attempt = 0;
        
        while ($attempt < $maxAttempts && $order->status === 'pending') {
            sleep(1);
            $order->refresh();
            $attempt++;
        }
        
        $status = $order->status === 'completed' ? 'success' : 'failed';
        
        return response()->json([
            'status' => $status,
            'order_id' => $order->id,
            'order_status' => $order->status,
            'message' => $this->getPaymentMessage($status)
        ]);
    }
    
    private function getPaymentMessage($status)
    {
        return match($status) {
            'success' => 'Payment completed successfully! Your order has been confirmed.',
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
