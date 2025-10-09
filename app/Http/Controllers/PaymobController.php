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
// comment test

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
        $success = $request->query('success') === 'true';
        $merchantOrderId = $request->query('merchant_order_id');

        $order = Order::find($merchantOrderId);

        if (!$order) {
            return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=not_found');
        }

        if ($success) {
            if ($order->status !== 'completed') {
                $order->update(['status' => 'completed']);
                \Log::info('✅ Payment redirect - Order marked as completed', ['order_id' => $order->id]);
            }

            return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=success&order_id=' . $order->id);
        } else {
            // Check if order was already completed by webhook before showing failed
            if ($order->status === 'completed') {
                return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=success&order_id=' . $order->id);
            }
            
            // Wait a moment to check if webhook completes the order
            sleep(2);
            $order->refresh();
            
            if ($order->status === 'completed') {
                return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=success&order_id=' . $order->id);
            }
            
            $order->update(['status' => 'payment_failed']);
            \Log::warning('❌ Payment redirect - Payment failed', ['order_id' => $order->id]);

            return redirect()->to(config('services.paymob.frontend_redirect_url') . '/payment-status?status=failed&order_id=' . $order->id);
        }
    }
// 
    
}
