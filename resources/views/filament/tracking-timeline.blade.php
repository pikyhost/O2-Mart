@php
    use App\Services\JeeblyService;

    $tracking = null;
    $timeline = [];

    if (isset($record) && $record->tracking_number) {
        try {
            $trackingData = (new JeeblyService())->trackShipment($record->tracking_number);
            $tracking = $trackingData['Tracking'] ?? null;
            $timeline = $tracking['events'] ?? []; // ✅ التعديل هنا
        } catch (\Exception $e) {
            $tracking = null;
        }
    }
@endphp

@if ($tracking)
    <div class="space-y-3">
        <div><strong>Status:</strong> {{ $tracking['last_status'] ?? 'N/A' }}</div>
        <div><strong>Pickup Date:</strong> {{ $tracking['pickup_date'] ?? 'N/A' }}</div>
        <div><strong>Booking Date:</strong> {{ $tracking['booking_date'] ?? 'N/A' }}</div>
        <div><strong>Failure Reason:</strong> {{ $timeline[0]['failure_reason'] ?? '-' }}</div>

        <div>
            <strong>Timeline:</strong>
            <ul class="list-disc ml-5 text-sm text-gray-700">
                @forelse ($timeline as $event)
                    <li>
                        <div><strong>{{ $event['status'] ?? '-' }}</strong></div>
                        @php
                            $translatedDesc = match($event['desc'] ?? '') {
                                'Consignment pickup_scheduled' => 'Pickup has been scheduled.',
                                'Consignment Softdata Uploaded' => 'Shipment data received.',
                                default => $event['desc'] ?? '-',
                            };
                        @endphp
                        <div>{{ $translatedDesc }}</div>


                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($event['event_date_time'])->format('d M Y - h:i A') }}
                        </div>
                    </li>
                @empty
                    <li>No timeline data available.</li>
                @endforelse
            </ul>
        </div>
    </div>
@else
    <div class="text-sm text-gray-500">Tracking information not available.</div>
@endif
