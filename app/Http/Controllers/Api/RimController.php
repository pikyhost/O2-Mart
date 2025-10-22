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
    private function processMarkdown($content)
    {
        if (!$content) return null;
        
        $lines = explode("\n", $content);
        $html = '';
        $inList = false;
        $listType = '';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            if (empty($trimmedLine)) {
                if ($inList) {
                    $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $inList = false;
                }
                $html .= "<br>\n";
                continue;
            }
            
            if (preg_match('/^(#{1,6})\s+(.+)/', $trimmedLine, $matches)) {
                if ($inList) {
                    $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $inList = false;
                }
                $level = strlen($matches[1]);
                $text = $this->processInlineMarkdown($matches[2]);
                $html .= "<h{$level}>{$text}</h{$level}>\n";
                continue;
            }
            
            if (preg_match('/^\*\s+(.+)/', $trimmedLine, $matches)) {
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $html .= "<ul>\n";
                    $inList = true;
                    $listType = 'ul';
                }
                $text = $this->processInlineMarkdown($matches[1]);
                $html .= "<li>{$text}</li>\n";
                continue;
            }
            
            if (preg_match('/^\d+\.\s+(.+)/', $trimmedLine, $matches)) {
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                    $html .= "<ol>\n";
                    $inList = true;
                    $listType = 'ol';
                }
                $text = $this->processInlineMarkdown($matches[1]);
                $html .= "<li>{$text}</li>\n";
                continue;
            }
            
            if ($inList) {
                $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
                $inList = false;
            }
            
            $text = $this->processInlineMarkdown($trimmedLine);
            $html .= "<p>{$text}</p>\n";
        }
        
        if ($inList) {
            $html .= ($listType === 'ul' ? '</ul>' : '</ol>') . "\n";
        }
        
        return trim($html);
    }

    private function processInlineMarkdown($text)
    {
        $text = preg_replace('/!\[([^\]]*)\]\(([^\)]+)\)/', '<img src="$2" alt="$1" style="max-width: 100%; height: auto;" />', $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        return $text;
    }

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
                    ->orWhere('colour', 'like', "%{$searchTerm}%")
                    ->orWhere('condition', 'like', "%{$searchTerm}%")
                    ->orWhere('bolt_pattern', 'like', "%{$searchTerm}%")
                    ->orWhere('offsets', 'like', "%{$searchTerm}%")
                    // Search in brand name
                    ->orWhereHas('rimBrand', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                    // Search in country name
                    ->orWhereHas('rimCountry', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                    // Search in rim size
                    ->orWhereHas('rimSize', function ($q) use ($searchTerm) {
                        $q->where('size', 'like', "%{$searchTerm}%");
                    })
                    // Search in category name
                    ->orWhereHas('category', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

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
            $finalPrice = $rim->discounted_price ?? $rim->regular_price;
            $rimArray['is_set_of_4'] = $finalPrice ? $finalPrice * 4 : 0;
            
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

        $rimArray = $rim->toArray();
        $rimArray['description'] = $this->processMarkdown($rim->description);
        $finalPrice = $rim->discounted_price ?? $rim->regular_price;
        $rimArray['is_set_of_4'] = $finalPrice ? $finalPrice * 4 : 0;
        
        return response()->json([
            'status' => 'success',
            'data' => array_merge(
                $rimArray,
                [
                    'photo_link' => $rim->rim_feature_image_url ?? null,
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
        Log::info('Filtering Rims with: ', ['request' => $request->all()]);

        $request->validate([
            'make_id' => 'nullable|integer',
            'model_id' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);

        $rims = Rim::whereHas('attributes', function ($query) use ($request) {
            if ($request->filled('make_id')) {
                $query->where('car_make_id', $request->make_id);
            }
            if ($request->filled('model_id')) {
                $query->where('car_model_id', $request->model_id);
            }
            if ($request->filled('year')) {
                $query->where('model_year', $request->year);
            }
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
                'colours' => Rim::query()->select('colour')->distinct()->whereNotNull('colour')->pluck('colour')->filter()->sort()->values(),
                'conditions' => Rim::query()->select('condition')->distinct()->whereNotNull('condition')->pluck('condition')->filter()->sort()->values(),
                'bolt_patterns' => Rim::query()->select('bolt_pattern')->distinct()->whereNotNull('bolt_pattern')->pluck('bolt_pattern')->filter()->sort()->values(),
                'offsets' => Rim::query()->select('offsets')->distinct()->whereNotNull('offsets')->pluck('offsets')->filter()->sort()->values(),
                'warranties' => Rim::query()->select('warranty')->distinct()->whereNotNull('warranty')->pluck('warranty')->filter()->sort()->values(),
                'brands' => RimBrand::select('id', 'name')->whereHas('rims')->orderBy('name')->get(),
                'countries' => RimCountry::select('id', 'name')->whereHas('rims')->orderBy('name')->get(),
                'sizes' => RimSize::select('id', 'size')->whereHas('rims')->orderBy('size')->get(),
                'categories' => Category::select('id', 'name')->whereHas('rims')->orderBy('name')->get(),
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
            })->sortBy('name')->values();

            return [
                'id' => $make->id,
                'name' => $make->name,
                'models' => $modelsGrouped
            ];
        })->sortBy('name')->values();

        return response()->json([
            'status' => 'success',
            'data' => $grouped
        ]);
    }


}
