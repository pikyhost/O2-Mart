<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JeeblyService
{
    protected string $baseUrl = 'https://demo.jeebly.com';
    protected string $apiKey;
    protected string $clientKey;

    public function __construct()
    {
        $this->apiKey = config('services.jeebly.api_key');
        $this->clientKey = config('services.jeebly.client_key');
    }

    public function createShipment(Order $order): ?array
    {

        \Log::info('Running createShipment for order ' . $order->id);

        $order->load('shippingAddress.city');

        // if (!$order->shippingAddress?->city?->is_supported_by_jeebly) {
        //     \Log::info('⛔ City not supported by Jeebly. Skipping shipment.', [
        //         'order_id' => $order->id,
        //         'city' => $order->shippingAddress?->city?->name
        //     ]);
        //     return null;
        // }
        Log::info('Running createShipment for order ' . $order->id);

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
        $deliveryItems = $order->items->whereIn('shipping_option', ['delivery_only', 'delivery_with_installation', 'with_installation', 'installation_center']);

        if ($deliveryItems->isEmpty()) {
            Log::info('⛔ No delivery items to send to Jeebly for order', ['order_id' => $order->id]);
            return null;
        }

        $cartFake->setRelation('items', $deliveryItems->map(function ($item) {

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


        Log::info('Jeebly Payload:', $payload);

        $response = Http::withHeaders([
            'x-api-key'  => $this->apiKey,
            'client_key' => $this->clientKey,
        ])->post("{$this->baseUrl}/customer/create_shipment", $payload);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Jeebly response data', $data);

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
            'x-api-key'  => $this->apiKey,
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
}
