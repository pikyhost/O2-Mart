<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tyre;
use App\Models\TyreAttribute;
use App\Models\TyreBrand;
use App\Models\TyreCountry;
use App\Models\TyreModel;

class TyreController extends Controller
{
    public function index(Request $request)
    {
        $query = Tyre::query();
        
        // Debug logging
        \Log::info('Tyre filter request', $request->all());

        if ($request->filled('tire_size')) {
            $query->where('tire_size', $request->tire_size);
        }

        if ($request->filled(['make_id', 'model_id', 'year'])) {
            $query->whereHas('tyreAttribute', function ($q) use ($request) {
                $q->where('car_make_id', $request->make_id)
                ->where('car_model_id', $request->model_id)
                ->where('model_year', (string) $request->year);

                if ($request->filled('trim')) {
                    $q->where('trim', $request->trim);
                }
                
                if ($request->filled('tyre_attribute') || $request->filled('rare_attribute')) {
                    $q->where(function($subQ) use ($request) {
                        if ($request->filled('tyre_attribute')) {
                            $subQ->where('tyre_attribute', $request->tyre_attribute)
                                 ->orWhere('rare_attribute', $request->tyre_attribute);
                        }
                        if ($request->filled('rare_attribute')) {
                            $subQ->orWhere('tyre_attribute', $request->rare_attribute)
                                 ->orWhere('rare_attribute', $request->rare_attribute);
                        }
                    });
                }
            });
        } elseif ($request->filled('tyre_attribute') || $request->filled('rare_attribute')) {
            $query->whereHas('tyreAttribute', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    if ($request->filled('tyre_attribute')) {
                        $subQ->where('tyre_attribute', $request->tyre_attribute)
                             ->orWhere('rare_attribute', $request->tyre_attribute);
                    }
                    if ($request->filled('rare_attribute')) {
                        $subQ->orWhere('tyre_attribute', $request->rare_attribute)
                             ->orWhere('rare_attribute', $request->rare_attribute);
                    }
                });
            });
        }

        if ($request->filled('tyre_oem')) {
            $query->whereHas('tyreAttribute', fn($q) => $q->where('tyre_oem', $request->tyre_oem));
        }

        if ($request->filled('width')) $query->where('width', $request->width);
        if ($request->filled('height')) $query->where('height', $request->height);
        if ($request->filled('tyre_size_id')) $query->where('tyre_size_id', $request->tyre_size_id);
        if ($request->filled('brand')) $query->where('tyre_brand_id', $request->brand);
        if ($request->filled('wheel_diameter')) $query->where('wheel_diameter', $request->wheel_diameter);
        if ($request->filled('model')) $query->where('tyre_model_id', $request->model);
        if ($request->filled('load_index')) $query->where('load_index', $request->load_index);
        if ($request->filled('speed_rating')) $query->where('speed_rating', $request->speed_rating);
        if ($request->filled('production_year')) $query->where('production_year', $request->production_year);
        if ($request->filled('country')) $query->where('tyre_country_id', $request->country);
        if ($request->filled('warranty')) $query->where('warranty', $request->warranty);

        $query->withAvg(['reviews as average_rating' => function ($q) {
            $q->where('is_approved', true);
        }], 'rating');

        // Debug: Log the final query
        \Log::info('Final tyre query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
        $tyres = $query->paginate(15);
        
        // Debug: Log results count
        \Log::info('Tyre results', ['total' => $tyres->total(), 'count' => $tyres->count()]);

        $tyres->load(['tyreBrand', 'tyreCountry', 'tyreAttribute']);
        
        $tyres->getCollection()->transform(function ($tyre) {
            $tyreArray = $tyre->toArray();
            $tyreArray['is_set_of_4'] = $tyre->price_vat_inclusive ? $tyre->price_vat_inclusive * 4 : 0;
            
            return array_merge(
                $tyreArray,
                [
                    'feature_image'   => $tyre->tyre_feature_image_url,
                    'secondary_image' => $tyre->tyre_secondary_image_url,
                    'gallery_images'  => $tyre->tyre_gallery_urls,
                    'average_rating'  => round($tyre->average_rating ?? 5, 2),
                    'buy_3_get_1_free'=> (bool) $tyre->buy_3_get_1_free,
                    'production_year' => $tyre->production_year,
                    'tyre_brand'      => $tyre->tyreBrand
                        ? array_merge(
                            $tyre->tyreBrand->only(['id', 'name']),
                            ['logo_url' => $tyre->tyreBrand->logo_url]
                        )
                        : null,
                    'country_of_origin' => $tyre->tyreCountry?->name,
                    'tyre_attribute'  => $tyre->tyreAttribute?->tyre_attribute,
                    'rare_attribute'  => $tyre->tyreAttribute?->rare_attribute,
                    'meta_title'       => $tyre->meta_title,
                    'meta_description' => $tyre->meta_description,
                    'alt_text'         => $tyre->alt_text,
                ]
            );
        });

        $collection = $tyres->getCollection();

        if ($request->filled(['make_id', 'model_id', 'year', 'tyre_attribute'])) {
            $data = $collection
                ->groupBy(function($tyre) {
                    $brandId = $tyre['tyre_brand']['id'] ?? 'unknown';
                    $year = $tyre['production_year'] ?? 'unknown';
                    $attr = $tyre['tyre_attribute'] ?? 'unknown';
                    return $brandId.'-'.$year.'-'.$attr;
                })
                ->map(function ($group) {
                    if ($group->count() >= 2) {
                        $setOf2 = $group->map(function ($tyre) {
                            $price = isset($tyre['discounted_price']) && $tyre['discounted_price'] > 0
                                ? (float)$tyre['discounted_price']
                                : (float)$tyre['price_vat_inclusive'];

                            $tyre['set_of_2_price'] = $price * 2;
                            return $tyre;
                        })->values();

                        $first = $setOf2->first();
                        return [
                            'brand' => $first['tyre_brand'] ?? null,
                            'production_year' => $first['production_year'] ?? null,
                            'tyres' => $setOf2,
                            'set_of_4_price' => $setOf2->sum('set_of_2_price'),
                            'cart_payload' => $setOf2->map(function ($tyre) {
                                return [
                                    'buyable_type' => 'tyre',
                                    'buyable_id'   => $tyre['id'],
                                    'quantity'     => 2,
                                ];
                            })->values(),
                        ];
                    }

                    return $group;
                })
                ->values();
        } else {
            $data = $collection;
        }

        return response()->json([
            'data' => [
                'current_page' => $tyres->currentPage(),
                'data'         => $data,
                'first_page_url' => $tyres->url(1),
                'from'         => $tyres->firstItem(),
                'last_page'    => $tyres->lastPage(),
                'last_page_url' => $tyres->url($tyres->lastPage()),
                'links'        => $tyres->linkCollection(),
                'next_page_url' => $tyres->nextPageUrl(),
                'path'         => $tyres->path(),
                'per_page'     => $tyres->perPage(),
                'prev_page_url' => $tyres->previousPageUrl(),
                'to'           => $tyres->lastItem(),
                'total'        => $tyres->total(),
            ]
        ]);
    }

    public function show($id)
    {
        $tyre = Tyre::with([
            'tyreAttribute',
            'tyreBrand',
            'tyreModel',
            'tyreSize',
            'tyreCountry',
            'reviews' => function ($q) {
                $q->where('is_approved', true)->latest();
            }
        ])->find($id);

        if (! $tyre) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tyre not found',
            ], 404);
        }

        $approvedReviews = $tyre->reviews;

        $ratingsBreakdown = $approvedReviews
            ->groupBy('rating')
            ->map(fn($group) => $group->count())
            ->sortKeysDesc();

        return response()->json([
            'status' => 'success',
            'data' => array_merge(
                $tyre->toArray(),
                [
                    'feature_image' => $tyre->tyre_feature_image_url,
                    'secondary_image' => $tyre->tyre_secondary_image_url,
                    'gallery_images' => $tyre->tyre_gallery_urls,
                    'average_rating' => round($approvedReviews->avg('rating'), 2),
                    'total_reviews' => $approvedReviews->count(),
                    'ratings_breakdown' => $ratingsBreakdown,
                    'buy_3_get_1_free' => (bool) $tyre->buy_3_get_1_free,
                    'production_year' => $tyre->production_year,
                    'is_set_of_4' => $tyre->price_vat_inclusive ? $tyre->price_vat_inclusive * 4 : 0,
                    'tyre_brand' => $tyre->tyreBrand
                        ? array_merge(
                            $tyre->tyreBrand->only(['id', 'name']),
                            ['logo_url' => $tyre->tyreBrand->logo_url]
                        )
                        : null,
                    'country_of_origin' => $tyre->tyreCountry?->name,
                    'reviews' => $approvedReviews->map(function ($review) {
                        return [
                            'name' => $review->name,
                            'summary' => $review->summary,
                            'review' => $review->review,
                            'rating' => $review->rating,
                            'created_at' => $review->created_at->diffForHumans(),
                        ];
                    }),
                    'meta_title'       => $tyre->meta_title,
                    'meta_description' => $tyre->meta_description,
                    'alt_text'         => $tyre->alt_text,

                ]
            ),
        ]);
    }



    public function searchByOem(Request $request)
    {
        $oem = $request->query('tyre_oem');

        $tyre = Tyre::whereHas('tyreAttribute', function ($query) use ($oem) {
            $query->where('tyre_oem', $oem);
        })->first();

        if (!$tyre) {
            return response()->json(['message' => 'Tyre not found'], 404);
        }

        return response()->json($tyre);
    }


    public function searchByAttribute(Request $request)
    {
        $attribute = $request->query('tyre_attribute');

        $tyre = Tyre::whereHas('tyreAttribute', function ($query) use ($attribute) {
            $query->where('tyre_attribute', $attribute);
        })->first();

        if (!$tyre) {
            return response()->json(['message' => 'Tyre not found'], 404);
        }

        return response()->json($tyre);
    }
    
    public function trims(Request $request)
    {
        $query = \App\Models\TyreAttribute::query();

        if ($request->filled('make')) {
            $query->whereHas('make', function ($q) use ($request) {
                $q->where('name', $request->make);
            });
        }

        if ($request->filled('model')) {
            $query->whereHas('model', function ($q) use ($request) {
                $q->where('name', $request->model);
            });
        }

        if ($request->filled('year')) {
            $query->where('model_year', $request->year);
        }

        $trims = $query->select('trim')->distinct()->pluck('trim')->filter()->values();

        return response()->json([
            'status' => 'success',
            'data' => $trims,
        ]);
    }

    public function searchBySizeOptions()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'widths' => Tyre::select('width')->distinct()->pluck('width')->filter()->values(),
                'heights' => Tyre::select('height')->distinct()->pluck('height')->filter()->values(),
                'wheel_diameters' => Tyre::select('wheel_diameter')->distinct()->pluck('wheel_diameter')->filter()->values(),
            ]
        ]);
    }

    public function brands()
    {
        return response()->json(['status' => 'success', 'data' => TyreBrand::select('id', 'name')->get()]);
    }

    public function widths()
    {
        $data = Tyre::select('width')->distinct()->pluck('width')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function heights()
    {
        $data = Tyre::select('height')->distinct()->pluck('height')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function wheelDiameters()
    {
        $data = Tyre::select('wheel_diameter')->distinct()->pluck('wheel_diameter')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function models()
    {
        return response()->json(['status' => 'success', 'data' => TyreModel::select('id', 'name')->get()]);
    }

    public function loadIndexes()
    {
        $data = Tyre::select('load_index')->distinct()->pluck('load_index')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function speedRatings()
    {
        $data = Tyre::select('speed_rating')->distinct()->pluck('speed_rating')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function productionYears()
    {
        $data = Tyre::select('production_year')->distinct()->pluck('production_year')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function countries()
    {
        return response()->json(['status' => 'success', 'data' => TyreCountry::select('id', 'name')->get()]);
    }

    public function warranties()
    {
        $data = Tyre::select('warranty')->distinct()->pluck('warranty')->filter()->values();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function compatibleCarMakes()
    {
        $attributes = \App\Models\TyreAttribute::with(['make', 'model', 'tyres'])->get();

        if ($attributes->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No compatible cars found for tyres.',
            ], 404);
        }

        $grouped = $attributes->filter(fn($attr) => $attr->tyres->isNotEmpty())
            ->groupBy('car_make_id')
            ->map(function ($group) {
                $make = $group->first()->make;
                
                if (!$make) {
                    return null;
                }

                $modelsGrouped = $group->groupBy('car_model_id')->map(function ($modelsGroup) {
                    $model = $modelsGroup->first()->model;
                    
                    if (!$model) {
                        return null;
                    }
                    
                    $yearsGrouped = $modelsGroup->groupBy('model_year')->map(function ($yearGroup, $year) {
                        $trims = $yearGroup->pluck('trim')->filter()->unique()->values();
                        return [
                            'year' => $year,
                            'trims' => $trims,
                        ];
                    })->values();

                    return [
                        'id' => $model->id,
                        'name' => $model->name,
                        'years' => $yearsGrouped,
                    ];
                })->filter()->values();

                return [
                    'id' => $make->id,
                    'name' => $make->name,
                    'models' => $modelsGrouped,
                ];
            })->filter()->values();

        return response()->json([
            'status' => 'success',
            'data' => $grouped,
        ]);
    }


    public function filters()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'brands' => \App\Models\TyreBrand::select('id', 'name')->get(),
                'widths' => Tyre::select('width')->distinct()->pluck('width')->filter()->values(),
                'heights' => Tyre::select('height')->distinct()->pluck('height')->filter()->values(),
                'wheel_diameters' => Tyre::select('wheel_diameter')->distinct()->pluck('wheel_diameter')->filter()->values(),
                'models' => \App\Models\TyreModel::select('id', 'name')->get(),
                'load_indexes' => Tyre::select('load_index')->distinct()->pluck('load_index')->filter()->values(),
                'speed_ratings' => Tyre::select('speed_rating')->distinct()->pluck('speed_rating')->filter()->values(),
                'production_years' => Tyre::select('production_year')->distinct()->pluck('production_year')->filter()->values(),
                'countries' => \App\Models\TyreCountry::select('id', 'name')->get(),
                'warranties' => Tyre::select('warranty')->distinct()->pluck('warranty')->filter()->values(),
            ],
        ]);
    }

    public function getCompatibleSizes(Request $request)
    {
        $request->validate([
            'make_id' => 'required|integer',
            'model_id' => 'required|integer',
            'year' => 'required',
            'trim' => 'nullable|string',
        ]);

        $query = \App\Models\TyreAttribute::query()
            ->where('car_make_id', $request->make_id)
            ->where('car_model_id', $request->model_id)
            ->where('model_year', $request->year);

        if ($request->filled('trim')) {
            $query->where('trim', $request->trim);
        }

        $tyreSizes = $query->with(['tyres.tyreSize', 'tyres.tyreAttribute'])
            ->get()
            ->pluck('tyres')
            ->flatten()
            ->filter(fn($tyre) => $tyre->tyreSize)
            ->map(function ($tyre) {
                return [
                    'id' => $tyre->tyreSize->id,
                    'name' => $tyre->tyreSize->size,
                    'oem' => (bool) $tyre->tyreAttribute?->tyre_oem, 
                ];
            })
            ->unique('id')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $tyreSizes,
        ]);
    }

   public function filterByCarAndTrim(Request $request)
{
    $makeId = $request->input('make_id');
    $modelId = $request->input('model_id');
    $year   = $request->input('year');
    $trim   = $request->input('trim');
    $tyreAttribute = $request->input('tyre_attribute'); 
    if (!$makeId || !$modelId || !$year) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Missing required parameters',
        ], 422);
    }

    $attributes = TyreAttribute::query()
        ->where('car_make_id', $makeId)
        ->where('car_model_id', $modelId)
        ->where('model_year', $year)
        ->when($trim, fn($q) => $q->where('trim', $trim))
        ->get();

    if ($attributes->isEmpty()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No attributes found for this car/trim',
        ]);
    }

    $result = [];

    foreach ($attributes as $attribute) {
        // فلترة لو الـ user اختار attribute معين
        if ($tyreAttribute && $attribute->tyre_attribute !== $tyreAttribute) {
            continue;
        }

        // جيب كل الإطارات المرتبطة بالـ attribute
        $tyres = \App\Models\Tyre::with(['tyreBrand', 'media'])
            ->where('tyre_attribute_id', $attribute->id)
            ->get();

        if ($tyres->isEmpty()) {
            continue;
        }

        // جروب حسب البراند والسنة
        $grouped = $tyres->groupBy(fn($tyre) => $tyre->tyre_brand_id . '-' . $tyre->production_year);

        foreach ($grouped as $group) {
            $first = $group->first();

            $tyreDetails = $group->map(function ($tyre) {
                return [
                    'id'                => $tyre->id,
                    'title'             => $tyre->title,
                    'description'       => $tyre->description,
                    'price'             => $tyre->price_vat_inclusive,
                    'discounted_price'  => $tyre->discounted_price,
                    'discount_percent'  => $tyre->discount_percentage,
                    'production_year'   => $tyre->production_year,
                    'warranty'          => $tyre->warranty,
                    'feature_image'     => $tyre->tyre_feature_image_url,
                    'secondary_image'   => $tyre->tyre_secondary_image_url,
                    'gallery_images'    => $tyre->tyre_gallery_urls,
                    'average_rating'    => $tyre->average_rating,
                    'brand'             => $tyre->tyreBrand?->name,
                    'brand_logo'        => $tyre->tyreBrand?->logo_url,
                    'position'          => $tyre->pivot->position ?? 'main',
                ];
            })->values();

            $setOf4Price = $group->sum(fn($tyre) => (float)$tyre->price_vat_inclusive * 2);

            $cartPayload = $group->map(function ($tyre) {
                return [
                    'buyable_type' => 'tyre',
                    'buyable_id'   => $tyre->id,
                    'quantity'     => 2, 
                ];
            })->values();

            $result[] = [
                'tyre_attribute' => $attribute->tyre_attribute,
                'rare_attribute' => $attribute->tyre_oem,
                'brand'          => $first->tyreBrand ? [
                    'id'   => $first->tyreBrand->id,
                    'name' => $first->tyreBrand->name,
                    'logo_url' => $first->tyreBrand->logo_url,
                ] : null,
                'production_year' => $first->production_year,
                'warranty'        => $first->warranty,
                'tyres'           => $tyreDetails,
                'set_of_4_price'  => $setOf4Price,
                'cart_payload'    => $cartPayload,
            ];
        }
    }

    return response()->json([
        'status' => 'success',
        'data'   => $result,
    ]);
}




    public function getAttributesByCarAndTrim(Request $request)
    {
        $request->validate([
            'make_id' => 'required|integer',
            'model_id' => 'required|integer',
            'year'     => 'required',
            'trim'     => 'nullable|string',
        ]);

        $query = \App\Models\TyreAttribute::where('car_make_id', $request->make_id)
            ->where('car_model_id', $request->model_id)
            ->where('model_year', $request->year);

        if ($request->filled('trim')) {
            $query->where('trim', $request->trim);
        }

        $attributes = $query->get(['tyre_attribute','rare_attribute','tyre_oem'])
            ->map(function ($attr) {
                return [
                    'main'  => $attr->tyre_attribute,
                    'rare'  => $attr->rare_attribute,
                    'is_oe' => (bool)$attr->tyre_oem,
                ];
            })
            ->unique(function ($item) {
                return $item['main'] .'-'. $item['rare'];
            })
            ->values();

        // Get car make and model names
        $make = \App\Models\CarMake::find($request->make_id);
        $model = \App\Models\CarModel::find($request->model_id);
        
        return response()->json([
            'status' => 'success',
            'selected' => [
                'make' => $make ? $make->name : null,
                'model' => $model ? $model->name : null,
                'year' => $request->year,
                'trim' => $request->trim
            ],
            'data'   => $attributes
        ]);
    }




}