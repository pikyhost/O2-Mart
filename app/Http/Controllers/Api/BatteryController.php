<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Battery;
use App\Models\BatteryAttribute;
use App\Models\BatteryBrand;
use App\Models\BatteryCapacity;
use App\Models\BatteryCountry;
use Illuminate\Http\Request;

class BatteryController extends Controller
{
public function index(Request $request)
{
    $query = Battery::with([
        'attributes',
        'batteryBrand',
        'capacity',
        'dimension',
        'batteryCountry',
        'category',
    ])->withAvg(['reviews as average_rating' => function ($q) {
        $q->where('is_approved', true);
    }], 'rating');

    // Global search functionality
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            // Search in product name, SKU, warranty, description
            $q->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('sku', 'like', "%{$searchTerm}%")
                ->orWhere('item_code', 'like', "%{$searchTerm}%")
                ->orWhere('warranty', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                // Search in brand name
                ->orWhereHas('batteryBrand', function ($q) use ($searchTerm) {
                    $q->where('value', 'like', "%{$searchTerm}%");
                })
                // Search in country name
                ->orWhereHas('batteryCountry', function ($q) use ($searchTerm) {
                    $q->where('value', 'like', "%{$searchTerm}%");
                })
                // Search in capacity
                ->orWhereHas('capacity', function ($q) use ($searchTerm) {
                    $q->where('value', 'like', "%{$searchTerm}%");
                })
                // Search in dimension
                ->orWhereHas('dimension', function ($q) use ($searchTerm) {
                    $q->where('value', 'like', "%{$searchTerm}%");
                })
                // Search in category name
                ->orWhereHas('category', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
        });
    }

    if ($request->filled('brand_id')) {
        $query->where('battery_brand_id', $request->brand_id);
    }

    if ($request->filled('capacity_id')) {
        $query->where('capacity_id', $request->capacity_id);
    }

    if ($request->filled('battery_country_id')) {
        $query->where('battery_country_id', $request->battery_country_id);
    }

    if ($request->filled('warranty')) {
        $query->where('warranty', $request->warranty);
    }

    $batteries = $query->get();

    $batteries->transform(function ($battery) {
        return array_merge(
            $battery->toArray(),
            [
                'feature_image' => $battery->battery_feature_image_url,
                'secondary_image' => $battery->battery_secondary_image_url,
                'gallery_images' => $battery->battery_gallery_urls,
                'average_rating' => round($battery->average_rating ?? 5, 2),
                'battery_brand' => $battery->batteryBrand
                ? array_merge(
                    $battery->batteryBrand->only(['id', 'value']),
                    ['logo_url' => $battery->batteryBrand->logo_url]
                )
                : null,
                'meta_title'       => $battery->meta_title,
                'meta_description' => $battery->meta_description,
                'alt_text'         => $battery->alt_text,

            ]
        );
    });

    return response()->json([
        'status' => 'success',
        'data' => $batteries,
    ]);
}


    public function searchByPartNumber(Request $request)
    {
        $request->validate([
            'part_number' => 'required|string',
        ]);

        $battery = Battery::where('part_number', $request->part_number)->first();

        if (! $battery) {
            return response()->json([
                'status' => 'error',
                'message' => 'Battery not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $battery,
        ]);
    }

    public function show($id)
    {
        $battery = Battery::with([
            'attributes',
            'batteryBrand',
            'capacity',
            'dimension',
            'batteryCountry',
            'category',
            'reviews' => function ($q) {
                $q->where('is_approved', true)->latest();
            }
        ])->find($id);

        if (! $battery) {
            return response()->json([
                'status' => 'error',
                'message' => 'Battery not found',
            ], 404);
        }

        $approvedReviews = $battery->reviews;

        $ratingsBreakdown = $approvedReviews
            ->groupBy('rating')
            ->map(fn($group) => $group->count())
            ->sortKeysDesc();

        return response()->json([
            'status' => 'success',
            'data' => array_merge(
                $battery->toArray(),
                [
                    'feature_image' => $battery->battery_feature_image_url ?? null,
                    'secondary_image' => $battery->battery_secondary_image_url ?? null,
                    'gallery_images' => $battery->battery_gallery_urls ?? [],
                    'average_rating' => round($approvedReviews->avg('rating'), 2),
                    'total_reviews' => $approvedReviews->count(),
                    'ratings_breakdown' => $ratingsBreakdown,
                    'battery_brand' => $battery->batteryBrand
                    ? array_merge(
                        $battery->batteryBrand->only(['id', 'value']),
                        ['logo_url' => $battery->batteryBrand->logo_url]
                    )
                    : null,
                    'meta_title'       => $battery->meta_title,
                    'meta_description' => $battery->meta_description,
                    'alt_text'         => $battery->alt_text,

                    'reviews' => $approvedReviews->map(function ($review) {
                        return [
                            'name' => $review->name,
                            'summary' => $review->summary,
                            'review' => $review->review,
                            'rating' => $review->rating,
                            'created_at' => $review->created_at->diffForHumans(),
                        ];
                    }),
                ]
            ),
        ]);
    }


    public function filters()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'brands' => BatteryBrand::select('id', 'value as name')->whereHas('batteries')->orderBy('value')->get(),
                'capacities' => BatteryCapacity::select('id', 'value as name')->whereHas('batteries')->orderBy('value')->get(),
                'countries' => BatteryCountry::select('id', 'name')->whereHas('batteries')->orderBy('name')->get(),
                'warranties' => Battery::select('warranty')->distinct()->whereNotNull('warranty')->pluck('warranty')->filter()->sort()->values(),
            ],
        ]);
    }

    public function compatibleSearch(Request $request)
    {
        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
        ]);

        $batteries = Battery::with([
            'attributes',
            'batteryBrand',
            'capacity',
            'dimension',
            'batteryCountry',
            'category',
        ])->whereHas('attributes', function ($query) use ($request) {
            $query->whereHas('make', fn($q) => $q->where('name', $request->make))
                  ->whereHas('model', fn($q) => $q->where('name', $request->model))
                  ->where('model_year', $request->year);
        })->withAvg(['reviews as average_rating' => function ($q) {
            $q->where('is_approved', true);
        }], 'rating')->get();

        $batteries->transform(function ($battery) {
            return array_merge(
                $battery->toArray(),
                [
                    'feature_image' => $battery->battery_feature_image_url,
                    'secondary_image' => $battery->battery_secondary_image_url,
                    'gallery_images' => $battery->battery_gallery_urls,
                    'average_rating' => round($battery->average_rating ?? 5, 2),
                    'battery_brand' => $battery->batteryBrand
                    ? array_merge(
                        $battery->batteryBrand->only(['id', 'value']),
                        ['logo_url' => $battery->batteryBrand->logo_url]
                    )
                    : null,
                    'meta_title'       => $battery->meta_title,
                    'meta_description' => $battery->meta_description,
                    'alt_text'         => $battery->alt_text,
                ]
            );
        });

        return response()->json([
            'status' => 'success',
            'data' => $batteries,
        ]);
    }


    public function compatibleCarMakes()
    {
        $attributes = BatteryAttribute::with(['make', 'model'])->get();

        if ($attributes->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No compatible cars found for batteries.',
            ], 404);
        }

        $grouped = $attributes->groupBy('car_make_id')->map(function ($group) {
            $make = $group->first()->make;

            $modelsGrouped = $group->groupBy('car_model_id')->map(function ($modelsGroup) {
                $model = $modelsGroup->first()->model;
                $years = $modelsGroup->pluck('model_year')->unique()->sort()->values();
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'years' => $years,
                ];
            })->sortBy('name')->values();

            return [
                'id' => $make->id,
                'name' => $make->name,
                'models' => $modelsGrouped,
            ];
        })->sortBy('name')->values();

        return response()->json([
            'status' => 'success',
            'data' => $grouped,
        ]);
    }

}
