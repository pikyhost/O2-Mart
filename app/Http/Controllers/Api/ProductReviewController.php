<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\AutoPart;
use App\Models\Tyre;
use App\Models\Battery;
use App\Models\Rim;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'summary' => 'required|string|max:255',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'product_type' => 'required|string|in:auto_part,tyre,battery,rim',
            'product_id' => 'required|integer',
        ]);

        $typeMap = [
            'auto_part' => AutoPart::class,
            'tyre' => Tyre::class,
            'battery' => Battery::class,
            'rim' => Rim::class,
        ];

        $productClass = $typeMap[$request->product_type] ?? null;
        if (!$productClass || !$productClass::find($request->product_id)) {
            return response()->json(['message' => 'Invalid product.'], 404);
        }

        $review = ProductReview::create([
            'name' => $request->name,
            'summary' => $request->summary,
            'review' => $request->review,
            'rating' => $request->rating,
            'product_type' => $productClass,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Review submitted, thank you.'], 201);
    }

    public function getProductReviews(Request $request)
    {
        $request->validate([
            'product_type' => 'required|string|in:auto_part,tyre,battery,rim',
            'product_id' => 'required|integer'
        ]);

        $typeMap = [
            'auto_part' => AutoPart::class,
            'tyre' => Tyre::class,
            'battery' => Battery::class,
            'rim' => Rim::class,
        ];

        $productClass = $typeMap[$request->product_type] ?? null;
        $productInstance = new $productClass;

        if (!$productInstance->find($request->product_id)) {
            return response()->json(['message' => 'Invalid product.'], 404);
        }


        $reviews = ProductReview::where('product_type', $productClass)
            ->where('product_id', $request->product_id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating');

        return response()->json([
            'average_rating' => round($averageRating, 2),
            'reviews' => $reviews,
        ]);
    }

    public function getAllReviews()
    {
        $reviews = ProductReview::with('product')
            ->where('is_approved', true)
            ->latest()
            ->get()
            ->map(function ($review) {
                $product = $review->product;

                return [
                    'name' => $review->name,
                    'rating' => $review->rating,
                    'summary' => $review->summary,
                    'review' => $review->review,
                    'product_type' => class_basename($review->product_type),
                    'product_id' => $review->product_id,
                    'product_title' => $product->title ?? $product->name ?? 'Unnamed',
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            })
            ->filter(); // remove nulls if any

        return response()->json([
            'status' => 'success',
            'data' => $reviews->values(), // reindex array
        ]);
    }


}
