<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\JeeblyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JeeblyWebhookController extends Controller
{
    public function handleStatusUpdate(Request $request)
    {


        $trackingNumber = $request->input('reference_number') ?? $request->input('tracking_number');
        
        if (!$trackingNumber) {

            return response()->json(['error' => 'Missing tracking number'], 400);
        }

        $order = Order::where('tracking_number', $trackingNumber)->first();
        
        if (!$order) {

            return response()->json(['error' => 'Order not found'], 404);
        }

        $jeeblyService = new JeeblyService();
        $updated = $jeeblyService->updateOrderStatus($order);

        if ($updated) {
            return response()->json(['message' => 'Order status updated successfully']);
        }

        return response()->json(['error' => 'Failed to update order status'], 500);
    }
}