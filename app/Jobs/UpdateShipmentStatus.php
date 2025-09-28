<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\JeeblyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateShipmentStatus implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->order->tracking_number) {
            Log::warning('Order has no tracking number', ['order_id' => $this->order->id]);
            return;
        }

        $jeebly = new JeeblyService();
        $trackingData = $jeebly->trackShipment($this->order->tracking_number);

        if (!$trackingData) {
            Log::error('Failed to fetch tracking info', ['order_id' => $this->order->id]);
            return;
        }

        $status = $trackingData['shipment_status'] ?? $trackingData['status'] ?? null;

        if ($status) {
            $this->order->update([
                'shipping_status' => $status,
            ]);

            Log::info('Updated shipping status', [
                'order_id' => $this->order->id,
                'status'   => $status,
            ]);
        }
    }
}
