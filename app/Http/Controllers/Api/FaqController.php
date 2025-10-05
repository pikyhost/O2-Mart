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

        $grouped = collect($items)->groupBy('category')->map(function ($items, $category) {
            $formattedCategory = html_entity_decode(ucwords(str_replace('_', ' ', $category)), ENT_QUOTES, 'UTF-8');
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
