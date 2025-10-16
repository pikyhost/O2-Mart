<?php

namespace App\Console\Commands;

use App\Jobs\UpdateShipmentStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class SyncJeeblyOrderStatus extends Command
{
    protected $signature = 'jeebly:sync-status {--order-id= : Sync specific order ID} {--limit=50 : Limit number of orders to sync}';
    protected $description = 'Sync order status with Jeebly tracking system';

    public function handle()
    {
        $orderId = $this->option('order-id');
        $limit = (int) $this->option('limit');

        if ($orderId) {
            $order = Order::find($orderId);
            if (!$order) {
                $this->error("Order {$orderId} not found");
                return 1;
            }
            
            if (!$order->tracking_number) {
                $this->error("Order {$orderId} has no tracking number");
                return 1;
            }

            $this->info("Syncing order {$orderId}...");
            UpdateShipmentStatus::dispatch($order);
            $this->info("Job dispatched for order {$orderId}");
            return 0;
        }

        $orders = Order::whereNotNull('tracking_number')
            ->whereIn('status', ['pending', 'preparing', 'shipping'])
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to sync');
            return 0;
        }

        $this->info("Found {$orders->count()} orders to sync");
        
        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            UpdateShipmentStatus::dispatch($order);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched {$orders->count()} jobs to sync order statuses");
        
        return 0;
    }
}