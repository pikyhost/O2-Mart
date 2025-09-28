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
            ]
        ]);
    }
}
