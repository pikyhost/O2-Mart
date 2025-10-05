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
                'General Information' => $grouped['general'] ?? [],
                'Pricing & Payment' => $grouped['payment'] ?? [],
                'Products & Services' => $grouped['products_services'] ?? [],
                'Returns & Missing Parts' => $grouped['returns_missing_parts'] ?? [],
                'Security & Privacy' => $grouped['security_privacy'] ?? [],
                'Registration & Account' => $grouped['registration_account'] ?? [],
                'Customer Support' => $grouped['customer_support'] ?? [],
                'Warranty' => $grouped['warranty'] ?? [],
                'Shipping' => $grouped['shipping'] ?? [],
                'Returns & Refunds' => $grouped['returns_refunds'] ?? [],
                'How to Order' => $grouped['how_to_order'] ?? [],
            ]
        ]);
    }
}
