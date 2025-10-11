<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaymobPaymentService
{
    /**
     * Create a new class instance.
     */

    protected $api_key;
    protected $integrations_id;
    protected $base_url;
    protected $header;
    public function __construct()
    {
        $this->base_url = config('services.paymob.base_url');
        $this->api_key = config('services.paymob.api_key');
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->integrations_id = [config('services.paymob.integration_id')];
    }

//first generate token to access api
    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', ['api_key' => $this->api_key]);
        $body = $response->getData(true);
        return $body['data']['token'] ?? $body['token'] ?? null;
    }

    public function sendPayment(Request $request): array
    {
        
        $authToken = $this->generateToken();
        Log::info('Auth Token Generated', ['token' => $authToken]);
        $this->header['Authorization'] = 'Bearer ' . $authToken;

        // Create Order
        $orderData = [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => $request->input('amount_cents'),
            'merchant_order_id' => $request->input('merchant_order_id'),
            'items' => [],
        ];

        $orderResponse = $this->buildRequest('POST', '/api/ecommerce/orders', $orderData);

        $body = $orderResponse->getData(true);
        $orderId = $body['id'] ?? ($body['data']['id'] ?? null);



        if (!$orderId) {
            Log::error('Order creation failed', [
                'response' => $body,
                'request_data' => $orderData,
                'auth_token' => $authToken ? 'present' : 'missing'
            ]);
            return ['success' => false, 'message' => 'Order creation failed.', 'paymob_response' => $body];
        }


        // Billing Data
        $user = auth()->user();

        $billingData = [
            "apartment"       => "NA",
            "email"           => $request->input('contact_email'),
            "floor"           => "NA",
            "first_name"      => $user?->name ?? $request->input('contact_name') ?? 'Guest',
            "street"          => "NA",
            "building"        => "NA",
            "phone_number"    => $user?->primaryAddress?->phone
                ?? $user?->addresses()?->latest()?->first()?->phone
                ?? $request->input('contact_phone')
                ?? '01000000000',
            "shipping_method" => "NA",
            "postal_code"     => "NA",
            "city"            => "NA",
            "country"         => "NA",
            "last_name"       => "NA",
            "state"           => "NA",
        ];

        Log::info('Billing Phone Number Used', ['phone' => $billingData['phone_number']]);

        // Generate Payment Key
        $paymentToken = $this->generatePaymentKey($authToken, $orderId, $request->input('amount_cents'), $billingData);
        Log::info('Payment Key Response', ['token' => $paymentToken]);

        if (!$paymentToken) {
            Log::error('Payment key generation failed');
            return ['success' => false, 'message' => 'Failed to generate payment key.'];
        }

        $iframeId = config('services.paymob.iframe_id');
        $iframeUrl = "{$this->base_url}/api/acceptance/iframes/{$iframeId}?payment_token={$paymentToken}";
        Log::info('Iframe URL Generated', ['url' => $iframeUrl]);

        return [
            'success' => true,
            'iframe_url' => $iframeUrl,
            'paymob_order_id' => $orderId,
        ];

    }

    protected function buildRequest($method, $endpoint, $data = [])
    {
        $client = new \GuzzleHttp\Client();
        $url = rtrim($this->base_url, '/') . $endpoint;

        try {
            $response = $client->request($method, $url, [
                'headers' => $this->header,
                'json'    => $data,
            ]);

            return response()->json(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            Log::error('Paymob Request Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Storage::put('paymob_response.json', json_encode($response));

        $paymobOrderId = $response['order'] ?? null;

        if (!$paymobOrderId) {
            \Log::error('Missing Paymob order ID in callback.');
            return false;
        }

        $order = \App\Models\Order::where('paymob_order_id', $paymobOrderId)->first();

        if (!$order) {
            \Log::error('No matching local order for Paymob order ID.', ['paymob_order_id' => $paymobOrderId]);
            return false;
        }

        if (isset($response['success']) && $response['success'] === 'true') {
        $order->update(['status' => 'completed']);

        try {
            Log::info('Calling Jeebly after payment', ['order_id' => $order->id]);

            (new \App\Services\JeeblyService())->createShipment($order);
            Log::info('Jeebly service called');

        } catch (\Exception $e) {
            \Log::error('Jeebly failed after payment', ['e' => $e->getMessage()]);
        }

        try {
            Mail::to($order->user?->email ?? $order->contact_email)->send(new \App\Mail\OrderReceiptMail($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send email receipt', ['e' => $e->getMessage()]);
        }

        \Log::info('Order marked as PAID & receipt sent', ['order_id' => $order->id]);
        return true;
    }else {
                $order->update(['status' => 'payment_failed']);
                \Log::warning('Order payment FAILED', ['order_id' => $order->id]);
                return false;
            }
    }




    protected function generatePaymentKey($authToken, $orderId, $amountCents, $billingData)
    {
        $data = [
            'auth_token' => $authToken,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => 'AED',
            'integration_id' => $this->integrations_id[0],
            'return_url'     => config('services.paymob.redirection_url', url('/api/payment/redirect')),
        ];

        $response = $this->buildRequest('POST', '/api/acceptance/payment_keys', $data);
        $body = $response->getData(true);



        return $body['token'] ?? $body['data']['token'] ?? null;
    }


}
