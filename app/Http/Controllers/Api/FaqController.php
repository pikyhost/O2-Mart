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

        $categoryMap = [
            'general' => 'General Information',
            'products_services' => 'Products & Services',
            'returns_missing_parts' => 'Returns & Missing Parts',
            'security_privacy' => 'Security & Privacy',
            'registration_account' => 'Registration & Account',
            'customer_support' => 'Customer Support',
            'warranty' => 'Warranty',
            'shipping' => 'Shipping',
            'returns_refunds' => 'Returns & Refunds',
            'payment' => 'Pricing & Payment',
            'how_to_order' => 'How to Order',
        ];

        $grouped = collect($items)->groupBy('category')->map(function ($items, $category) use ($categoryMap) {
            $formattedCategory = $categoryMap[$category] ?? ucwords(str_replace('_', ' ', $category));
            return [
                'category' => $formattedCategory,
                'items' => $items
            ];
        })->toArray();

        return response()->json([
            'status' => 'success',
            'data' => [
                'background_image' => $faq->background_image ? asset('storage/' . $faq->background_image) : null,
                'categories' => $grouped
            ]
        ]);
    }
}
