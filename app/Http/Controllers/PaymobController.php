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
        Log::info('📩 Webhook Received', ['data' => $request->all()]);

        $data = $request->all();

        $isPaid = $data['success'] ?? false;
        $orderId = $data['merchant_order_id'] ?? null;

        if ($isPaid && $orderId) {
            $order = Order::with('user', 'items')->find($orderId);

            if ($order) {
                if ($order->status !== 'completed') {
                    $order->update(['status' => 'completed']);

                    try {
                        if (!$order->tracking_number) {
                            $shippingService = new JeeblyService();
                            $shippingService->createShipment($order);
                            Log::info('🚚 Shipment created from webhook', ['order_id' => $order->id]);
                        }

                        $email = $order->contact_email ?? $order->user?->email;
                        if ($email) {
                            Mail::to($email)->send(new OrderReceiptMail($order));
                        }

                        Log::info('✅ Order receipt email sent', ['order_id' => $order->id]);
                    } catch (\Exception $e) {
                        Log::error('❌ Failed in webhook processing', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    Log::info('ℹ️ Order already marked as completed', ['order_id' => $order->id]);
                }
            } else {
                Log::warning('⚠️ Order not found for webhook', ['order_id' => $orderId]);
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
        
        // Wait a moment for webhook to process if order is still pending
        if ($order->status === 'pending') {
            sleep(3);
            $order->refresh();
        }
        
        $status = $order->status === 'completed' ? 'success' : 'failed';
        
        return response()->json([
            'status' => $status,
            'order_id' => $order->id,
            'order_status' => $order->status
        ]);
    }
    
    public function testPayment()
    {
        $service = new PaymobPaymentService();
        
        $testRequest = new \Illuminate\Http\Request();
        $testRequest->merge([
            'amount_cents' => 10000, // 100 AED
            'contact_email' => 'test@example.com',
            'name' => 'Test User',
            'merchant_order_id' => 'TEST_' . time(),
            'phone_number' => '01000000000',
        ]);
        
        $result = $service->sendPayment($testRequest);
        
        if ($result['success']) {
            return redirect($result['iframe_url']);
        }
        
        return response()->json(['error' => 'Failed to create test payment']);
    }
}
