<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JeeblyService;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function track(Request $request)
    {
        $trackingNumber = $request->get('tracking_number');

        if (!$trackingNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking number is required.',
            ], 400);
        }

        return $this->formatTrackingResponse($trackingNumber);
    }

    public function trackByNumber(string $trackingNumber)
    {
        return $this->formatTrackingResponse($trackingNumber);
    }

    private function formatTrackingResponse(string $trackingNumber)
    {
        try {
            $data = (new JeeblyService())->trackShipment($trackingNumber);

            if (!isset($data['Tracking'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking information not found.',
                ], 404);
            }

            $tracking = $data['Tracking'];
            $events = collect($tracking['events'] ?? [])->map(function ($event) {
                $dateTime = Carbon::parse($event['event_date_time']);
                return [
                    'date' => $dateTime->format('d-M-Y'),
                    'time' => $dateTime->format('h:i A'),
                    'status' => $event['status'] ?? '',
                    'description' => match($event['desc'] ?? '') {
                        'Consignment pickup_scheduled' => 'Pickup has been scheduled.',
                        'Consignment Softdata Uploaded' => 'Shipment data received.',
                        'Consignment is delivered' => 'Delivered successfully.',
                        'Consignment is out for delivery' => 'Out for delivery.',
                        'Consignment has been inscanned at hub' => 'Arrived at hub.',
                        'Consignment has been picked up' => 'Pickup completed.',
                        default => $event['desc'] ?? '-',
                    },
                    'hub' => $event['hub_name'] ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'tracking_number' => $trackingNumber,
                'status' => $tracking['last_status'] ?? null,
                'pickup_date' => $tracking['pickup_date'] ?? null,
                'booking_date' => $tracking['booking_date'] ?? null,
                'timeline' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
