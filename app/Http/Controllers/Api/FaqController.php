<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
     public function index()
    {
        $faq = Faq::first(); 

        $items = $faq->items ?? [];

        $grouped = collect($items)->groupBy('category')->toArray();

        return response()->json([
            'status' => 'success',
            'data' => [
                'background_image' => $faq->background_image ? asset('storage/' . $faq->background_image) : null,
                'general' => $grouped['general'] ?? [],
                'payment' => $grouped['payment'] ?? [],
                'products_services' => $grouped['products_services'] ?? [],
                'returns_missing_parts' => $grouped['returns_missing_parts'] ?? [],
                'security_privacy' => $grouped['security_privacy'] ?? [],
                'registration_account' => $grouped['registration_account'] ?? [],
                'customer_support' => $grouped['customer_support'] ?? [],
                'warranty' => $grouped['warranty'] ?? [],
                'shipping' => $grouped['shipping'] ?? [],
                'returns_refunds' => $grouped['returns_refunds'] ?? [],
                'how_to_order' => $grouped['how_to_order'] ?? [],
            ]
        ]);
    }
}
