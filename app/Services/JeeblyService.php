<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Services\ShippingCalculatorService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JeeblyService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $clientKey;

    public function __construct()
    {
        $this->baseUrl = config('services.jeebly.base_url', 'https://demo.jeebly.com');
        $this->apiKey = config('services.jeebly.api_key');
        $this->clientKey = config('services.jeebly.client_key');
    }

    public function createShipment(Order $order): ?array
    {

        \Log::info('Running createShipment for order ' . $order->id, []);

        $order->load('shippingAddress.city');

        // if (!$order->shippingAddress?->city?->is_supported_by_jeebly) {
        //     \Log::info('⛔ City not supported by Jeebly. Skipping shipment.', [
        //         'order_id' => $order->id,
        //         'city' => $order->shippingAddress?->city?->name
        //     ]);
        //     return null;
        // }
        Log::info('Running createShipment for order ' . $order->id, []);

        $pickupDate = now()->addDay();
        if ($pickupDate->isSunday()) {
            $pickupDate->addDay();
        }

        $order->load('user');
        $shippingAddress = $order->addresses()
            ->where('type', 'shipping')
            ->with(['area', 'city'])
            ->first();

        if (!$shippingAddress) return null;

        $cartFake = new \App\Models\Cart([
            'area_id' => $order->shippingAddress->area_id,
        ]);
        $deliveryOnlyItems = $order->items->where('shipping_option', 'delivery_only');

        if ($deliveryOnlyItems->isEmpty()) {
            Log::info('⛔ No delivery_only items to send to Jeebly for order', ['order_id' => $order->id]);
            return null;
        }

        $cartFake->setRelation('items', $deliveryOnlyItems->map(function ($item) {

            return (object)[
                'buyable' => $item->buyable,
                'quantity' => $item->quantity,
            ];
        }));

        $shipmentCount = $order->user?->shipment_count ?? 20;
            $shipping = ShippingCalculatorService::calculate($cartFake, $shipmentCount);
        // $cart = Cart::find($order->cart_id);
        $weight = 1.0;

        $payload = [
            "delivery_type"                           => "Next Day",
            "load_type"                               => "Non-document",
            "consignment_type"                        => "FORWARD",
            "description"                             => "O2Mart Order #{$order->id}",
            "weight"                                  => $weight ?: 1.0,
            "payment_type"                            => $order->payment_method === 'cod' ? 'COD' : 'Prepaid',
            "cod_amount"                              => $order->payment_method === 'cod' ? $order->total : 0,
            "num_pieces"                              => 1,
            "customer_reference_number"               => "ORDER_{$order->id}",

            // Origin
            "origin_address_name"                     => "O2Mart Warehouse",
            "origin_address_mob_no_country_code"      => "971",
            "origin_address_mobile_number"            => "500000000",
            "origin_address_alt_ph_country_code"      => "",
            "origin_address_alternate_phone"          => "",
            "origin_address_house_no"                 => "W5",
            "origin_address_building_name"            => "Warehouse 5",
            "origin_address_area"                     => "Al Quoz",
            "origin_address_landmark"                 => "Near XYZ",
            "origin_address_city"                     => "Dubai",
            "origin_address_type"                     => "Normal",

            // Destination
            "destination_address_name"                => optional($order->user)->name
                ?? $order->contact_name
                ?? 'To Be Updated',
            "destination_address_mob_no_country_code" => "971",
            "destination_address_mobile_number"       => $shippingAddress->phone ?: "0000000000",
            "destination_address_alt_ph_country_code" => "",
            "destination_address_alternate_phone"     => "",
            "destination_address_house_no"            => $shippingAddress->house_no ?? "To Be Updated",
            "destination_address_building_name"       => $shippingAddress->building_name ?? "To Be Updated",
            "destination_address_area"                => $shippingAddress->area?->name ?? "To Be Updated",
            "destination_address_landmark"            => $shippingAddress->landmark ?? "To Be Updated",
            "destination_address_city"                => $shippingAddress->city?->jeebly_name 
                                                        ?? $shippingAddress->city?->name 
                                                        ?? "Dubai",
            "destination_address_type"                => "Normal",

            "pickup_date" => $pickupDate->format('Y-m-d'),
        ];


        Log::info('Jeebly Payload:', ['payload' => $payload]);

        $response = Http::withHeaders([
            'x-api-key'  => $this->apiKey,
            'client_key' => $this->clientKey,
        ])->post("{$this->baseUrl}/customer/create_shipment", $payload);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Jeebly response data', ['data' => $data]);

            $awb = $data['AWB No'] ?? $data['AWBNo'] ?? null;

            $order->update([
                'tracking_number' => $awb,
                'tracking_url'    => $awb ? "https://demo.jeebly.com/track-shipment?shipment_number={$awb}" : null,
                'shipping_company' => 'Jeebly',
            ]);

            return $data;
        }

        Log::error('Jeebly createShipment failed', [
            'body' => $response->body(),
            'payload' => $payload,
        ]);

        return null;
    }

    public function trackShipment(string $shipmentNumber): ?array
    {
        $response = Http::withHeaders([
            'X-API-KEY'  => $this->apiKey,
            'client_key' => $this->clientKey,
        ])->post("{$this->baseUrl}/customer/track_shipment", [
            'reference_number' => $shipmentNumber,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Jeebly shipment tracking failed', ['response' => $response->body()]);
        return null;
    }

    public function updateOrderStatus(Order $order): bool
    {
        if (!$order->tracking_number) {
            return false;
        }

        $trackingData = $this->trackShipment($order->tracking_number);
        
        if (!$trackingData || $trackingData['success'] !== 'true') {
            return false;
        }

        $tracking = $trackingData['Tracking'] ?? [];
        $lastStatus = $tracking['last_status'] ?? null;
        
        if (!$lastStatus) {
            return false;
        }

        $orderStatus = $this->mapJeeblyStatusToOrderStatus($lastStatus);
        $shippingStatus = $this->mapJeeblyStatusToShippingStatus($lastStatus);
        
        $order->update([
            'status' => $orderStatus,
            'shipping_status' => $shippingStatus,
            'shipping_response' => $trackingData,
        ]);

        Log::info('Updated order status from Jeebly', [
            'order_id' => $order->id,
            'jeebly_status' => $lastStatus,
            'order_status' => $orderStatus,
            'shipping_status' => $shippingStatus,
        ]);

        return true;
    }

    private function mapJeeblyStatusToOrderStatus(string $jeeblyStatus): string
    {
        return match (strtolower($jeeblyStatus)) {
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
            'rto_delivered' => 'refund',
            default => 'shipping',
        };
    }

    private function mapJeeblyStatusToShippingStatus(string $jeeblyStatus): string
    {
        return match (strtolower($jeeblyStatus)) {
            'pickup scheduled' => 'pickup_scheduled',
            'pickup completed', 'pickupcompleted' => 'pickup_completed',
            'inscan at hub' => 'at_hub',
            'reached at hub' => 'at_hub',
            'out for delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'undelivered' => 'delivery_failed',
            'on – hold', 'on-hold' => 'on_hold',
            'rto' => 'returning',
            'rto delivered' => 'returned',
            'cancelled' => 'cancelled',
            default => strtolower(str_replace(' ', '_', $jeeblyStatus)),
        };
    }
}
