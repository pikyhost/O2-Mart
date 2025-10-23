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



        $order->load('addresses.city');

        // Check if city is supported by Jeebly (if you want to enable this later)
        // $shippingAddr = $order->addresses()->where('type', 'shipping')->first();
        // if (!$shippingAddr?->city?->is_supported_by_jeebly) {
        //     \Log::info('⛔ City not supported by Jeebly. Skipping shipment.', [
        //         'order_id' => $order->id,
        //         'city' => $shippingAddr?->city?->name
        //     ]);
        //     return null;
        // }
        


        $pickupDate = now()->addDay();
        if ($pickupDate->isSunday()) {
            $pickupDate->addDay();
        }

        $order->load('user');
        $shippingAddress = $order->addresses()
            ->where('type', 'shipping')
            ->with(['area', 'city'])
            ->first();

        if (!$shippingAddress) {
            Log::warning('No shipping address found for order', ['order_id' => $order->id]);
            return null;
        }

        $cartFake = new \App\Models\Cart([
            'area_id' => $shippingAddress->area_id,
        ]);
        $deliveryOnlyItems = $order->items->where('shipping_option', 'delivery_only');

        if ($deliveryOnlyItems->isEmpty()) {
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






        $response = Http::withHeaders([
            'X-API-KEY'  => $this->apiKey,
            'client_key' => $this->clientKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/customer/create_shipment", $payload);



        if ($response->successful()) {
            $data = $response->json();


            // Check if response contains error or success indicator
            if (isset($data['success']) && $data['success'] === false) {
                Log::error('Jeebly API returned error', ['response' => $data]);
                return null;
            }

            // Check for various AWB field names
            $awb = $data['AWB No'] ?? $data['AWBNo'] ?? $data['awb'] ?? $data['tracking_number'] ?? null;
            
            // Also check nested data structures
            if (!$awb && isset($data['data'])) {
                $awb = $data['data']['AWB No'] ?? $data['data']['AWBNo'] ?? $data['data']['awb'] ?? null;
            }

            if ($awb) {
                $order->update([
                    'tracking_number' => $awb,
                    'tracking_url'    => "https://demo.jeebly.com/track-shipment?shipment_number={$awb}",
                    'shipping_company' => 'Jeebly',
                ]);
            } else {
                Log::warning('No AWB number in Jeebly response', [
                    'response' => $data,
                    'checked_fields' => ['AWB No', 'AWBNo', 'awb', 'tracking_number', 'data.AWB No', 'data.AWBNo', 'data.awb']
                ]);
            }

            return $data;
        }

        Log::error('Jeebly createShipment failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'payload' => $payload,
            'api_key_present' => $this->apiKey ? true : false,
            'client_key_present' => $this->clientKey ? true : false,
        ]);

        // Check if wallet balance is low
        $this->checkAndNotifyLowWallet($response, $order);

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

    /**
     * Check if wallet balance is low and notify super admins
     */
    private function checkAndNotifyLowWallet($response, Order $order): void
    {
        $responseBody = $response->body();
        $responseStatus = $response->status();
        
        // Check if response contains low wallet balance message
        $isLowWallet = $responseStatus == 400 && 
                       (str_contains(strtolower($responseBody), 'wallet balance is low') || 
                        str_contains(strtolower($responseBody), 'insufficient balance'));
        
        if (!$isLowWallet) {
            return;
        }

        // Prevent duplicate notifications - check cache first
        $cacheKey = "jeebly_low_wallet_notified";
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return;
        }

        // Mark as notified for 1 hour to prevent duplicates
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHour());

        Log::critical('Jeebly Wallet Balance is LOW!', [
            'order_id' => $order->id,
            'message' => 'Wallet balance is low'
        ]);

        // Prepare error data
        $errorData = [
            'status' => $responseStatus,
            'message' => 'Wallet balance is low',
            'order_id' => $order->id,
            'response_body' => $responseBody,
        ];

        // Get all super admin users
        $superAdmins = \App\Models\User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super_admin', 'admin']);
        })->get();

        if ($superAdmins->isEmpty()) {
            Log::warning('No super admin users found to notify about low Jeebly wallet');
            return;
        }

        // Send email and Filament notification to each super admin
        foreach ($superAdmins as $admin) {
            // Send email notification directly
            try {
                \Illuminate\Support\Facades\Mail::to($admin->email)
                    ->send(new \App\Mail\JeeblyLowWalletAlert($errorData));
            } catch (\Exception $e) {
                Log::error('Failed to send low wallet email', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Create Filament notification in database
            try {
                \Filament\Notifications\Notification::make()
                    ->title('⚠️ Jeebly Wallet Balance is Low')
                    ->body("Shipment creation failed for Order #{$order->id}. Please recharge the Jeebly wallet immediately.")
                    ->danger()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->persistent()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view_order')
                            ->label('View Order')
                            ->url(route('filament.admin.resources.orders.view', ['record' => $order->id]))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            } catch (\Exception $e) {
                Log::error('Failed to create Filament notification', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
