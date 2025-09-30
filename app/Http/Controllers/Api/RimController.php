<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rim;
use App\Models\RimAttribute;
use App\Models\RimBrand;
use App\Models\RimCountry;
use App\Models\RimSize;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RimController extends Controller
{

    public function index(Request $request)
    {
        $query = Rim::with([
            'attributes.make',
            'attributes.model',
            'rimBrand',
            'rimSize',
            'rimCountry',
            'category',
        ])->withAvg(['reviews as average_rating' => function ($q) {
                $q->where('is_approved', true);
            }], 'rating');
        if ($request->filled('colour')) {
            $query->where('colour', $request->colour);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('bolt_pattern')) {
            $query->where('bolt_pattern', $request->bolt_pattern);
        }

        if ($request->filled('offsets')) {
            $query->where('offsets', $request->offsets);
        }


        if ($request->filled('warranty')) {
            $query->where('warranty', $request->warranty);
        }

        if ($request->filled('brand_id')) {
            $query->where('rim_brand_id', $request->brand_id);
        }

        if ($request->filled('country_id')) {
            $query->where('rim_country_id', $request->country_id);
        }

        $rims = $query->paginate(15);

        $rims->getCollection()->transform(function ($rim) {
            $rimArray = $rim->toArray();
            $rimArray['is_set_of_4'] = $rim->regular_price ? $rim->regular_price * 4 : 0;
            
            return array_merge(
                $rimArray,
                [
                    'feature_image' => $rim->rim_feature_image_url,
                    'secondary_image' => $rim->rim_secondary_image_url,
                    'gallery_images' => $rim->rim_gallery_urls,
                    'average_rating' => round($rim->average_rating ?? 5, 2),
                    'rim_brand' => $rim->rimBrand
                        ? array_merge(
                            $rim->rimBrand->only(['id', 'name']),
                            ['logo_url' => $rim->rimBrand->logo_url]
                        )
                        : null,
                    'meta_title' => $rim->meta_title,
                    'meta_description' => $rim->meta_description,
                    'alt_text' => $rim->alt_text,

                ]
            );
        });


        return response()->json([
            'data' => [
                'current_page' => $rims->currentPage(),
                'data'         => $rims->items(),
                'first_page_url' => $rims->url(1),
                'from'         => $rims->firstItem(),
                'last_page'    => $rims->lastPage(),
                'last_page_url' => $rims->url($rims->lastPage()),
                'links'        => $rims->linkCollection(),
                'next_page_url' => $rims->nextPageUrl(),
                'path'         => $rims->path(),
                'per_page'     => $rims->perPage(),
                'prev_page_url' => $rims->previousPageUrl(),
                'to'           => $rims->lastItem(),
                'total'        => $rims->total(),
            ]
        ]);
    }


    public function searchByPartNumber(Request $request)
    {
        $request->validate([
            'part_number' => 'required|string',
        ]);

        $rim = Rim::where('part_number', $request->part_number)->first();

        if (! $rim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rim not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $rim,
        ]);
    }
    public function show($id)
    {
        $rim = Rim::with([
            'attributes.make',
            'attributes.model',
            'rimBrand',
            'rimSize',
            'rimCountry',
            'category',
            'reviews' => function ($q) {
                $q->where('is_approved', true)->latest();
            }
        ])->find($id);

        if (! $rim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rim not found',
            ], 404);
        }

        $approvedReviews = $rim->reviews;

        $ratingsBreakdown = $approvedReviews
            ->groupBy('rating')
            ->map(fn($group) => $group->count())
            ->sortKeysDesc();

        return response()->json([
            'status' => 'success',
            'data' => array_merge(
                $rim->toArray(),
                [
                    'feature_image' => $rim->rim_feature_image_url ?? null,
                    'secondary_image' => $rim->rim_secondary_image_url ?? null,
                    'gallery_images' => $rim->rim_gallery_urls ?? [],
                    'average_rating' => round($approvedReviews->avg('rating'), 2),
                    'total_reviews' => $approvedReviews->count(),
                    'ratings_breakdown' => $ratingsBreakdown,
                    'rim_brand' => $rim->rimBrand
                    ? array_merge(
                        $rim->rimBrand->only(['id', 'name']),
                        ['logo_url' => $rim->rimBrand->logo_url]
                    )
                    : null,
                    'weight_kg' => $rim->weight ?? null,
                    'wheel_attribute' => $rim->rimAttribute ? $rim->rimAttribute->name : null,
                    'reviews' => $approvedReviews->map(function ($review) {
                        return [
                            'name' => $review->name,
                            'summary' => $review->summary,
                            'review' => $review->review,
                            'rating' => $review->rating,
                            'created_at' => $review->created_at->diffForHumans(),
                        ];
                    }),
                    'meta_title' => $rim->meta_title,
                    'meta_description' => $rim->meta_description,
                    'alt_text' => $rim->alt_text,

                ]
            ),
        ]);
    }


    public function compatibleSearch(Request $request)
    {
        Log::info('Filtering Rims with: ', $request->all());

        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
        ]);

        $rims = Rim::whereHas('attributes', function ($query) use ($request) {
            $query->whereHas('make', fn($q) => $q->where('name', $request->make))
                ->whereHas('model', fn($q) => $q->where('name', $request->model))
                ->where('model_year', $request->year);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $rims,
        ]);
    }

    public function filters()
    {
        return response()->json([
            'status' => 'success',
            'filters' => [
                'colours' => Rim::query()->select('colour')->distinct()->whereNotNull('colour')->pluck('colour')->filter(),
                'conditions' => Rim::query()->select('condition')->distinct()->whereNotNull('condition')->pluck('condition')->filter(),
                'bolt_patterns' => Rim::query()->select('bolt_pattern')->distinct()->whereNotNull('bolt_pattern')->pluck('bolt_pattern')->filter(),
                'offsets' => Rim::query()->select('offsets')->distinct()->whereNotNull('offsets')->pluck('offsets')->filter(),
                'warranties' => Rim::query()->select('warranty')->distinct()->whereNotNull('warranty')->pluck('warranty')->filter(),
                'brands' => RimBrand::select('id', 'name')->whereHas('rims')->get(),
                'countries' => RimCountry::select('id', 'name')->whereHas('rims')->get(),
                'sizes' => RimSize::select('id', 'size')->whereHas('rims')->get(),
                'categories' => Category::select('id', 'name')->whereHas('rims')->get(),
            ],
        ]);
    }


    public function compatibleCarMakes()
    {
        $attributes = RimAttribute::with(['make', 'model'])->get();

        $grouped = $attributes->groupBy('car_make_id')->map(function ($group) {
            $make = $group->first()->make;

            $modelsGrouped = $group->groupBy('car_model_id')->map(function ($modelsGroup) {
                $model = $modelsGroup->first()->model;
                $years = $modelsGroup->pluck('model_year')->unique()->sort()->values();
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'years' => $years
                ];
            })->values();

            return [
                'id' => $make->id,
                'name' => $make->name,
                'models' => $modelsGrouped
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $grouped
        ]);
    }


}
