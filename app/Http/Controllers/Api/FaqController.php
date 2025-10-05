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

        $grouped = collect($items)->groupBy('category')->map(function($items, $category) {
            return [
                'category_name' => ucwords(str_replace('_', ' ', $category)),
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
