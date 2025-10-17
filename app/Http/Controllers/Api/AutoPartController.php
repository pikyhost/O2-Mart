<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoPart;
use App\Models\AutoPartBrand;
use App\Models\AutoPartCountry;
use App\Models\Category;
use App\Models\ViscosityGrade;
use Illuminate\Http\Request;

class AutoPartController extends Controller
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
        $query = AutoPart::with([
            'autoPartBrand',
            'autoPartCountry',
            'category',
            'viscosityGrade',
        ])
        ->withAvg(['reviews as average_rating' => function ($q) {
            $q->where('is_approved', true);
        }], 'rating');

        if ($request->filled('part_number')) {
            $query->where('part_number', $request->part_number);
        }

        if ($request->filled('cross_reference')) {
            $query->whereJsonContains('cross_reference_numbers', $request->cross_reference);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('auto_part_brand_id', $request->brand_id);
        }

        if ($request->filled('country_id')) {
            $query->where('auto_part_country_id', $request->country_id);
        }

        if ($request->filled('viscosity_grade_id')) {
            $query->where('viscosity_grade_id', $request->viscosity_grade_id);
        }

        if ($request->filled('price_min')) {
            $query->where('discounted_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('discounted_price', '<=', $request->price_max);
        }

        $data = $query->paginate(15);

        $data->getCollection()->transform(fn ($item) => $this->transformAutoPart($item, withReviews: false));

        return response()->json([
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $autoPart = AutoPart::with([
            'autoPartBrand',
            'autoPartCountry',
            'category',
            'viscosityGrade',
            'reviews' => fn ($q) => $q->where('is_approved', true)->latest(),
        ])->find($id);

        if (! $autoPart) {
            return response()->json([
                'message' => 'Auto Part not found',
            ], 404);
        }

        return response()->json([
            'data' => $this->transformAutoPart($autoPart, withReviews: true),
        ]);
    }

    private function transformAutoPart(AutoPart $item, bool $withReviews = false): array
    {
        // Use media library for photo_link but keep same key name
        $photoLink = $item->getAutoPartFeatureImageUrl();

        $approvedReviews = collect();
        if ($withReviews) {
            $approvedReviews = $item->relationLoaded('reviews')
                ? $item->reviews
                : $item->reviews()->where('is_approved', true)->latest()->get();
        }

        $average = $withReviews
            ? round(($approvedReviews->avg('rating') ?? 5), 2)
            : round(($item->average_rating ?? 5), 2);

        $itemArray = $item->toArray();
        // Process markdown fields
        $itemArray['description'] = $this->processMarkdown($item->description);
        $itemArray['details'] = $this->processMarkdown($item->details);
        // Remove null brand_id and country_id fields
        unset($itemArray['brand_id'], $itemArray['country_id']);
        
        $base = array_merge(
            $itemArray,
            [
                'photo_link'        => $photoLink,
                'average_rating'    => $average,
                'auto_part_brand'   => $item->autoPartBrand
                    ? array_merge(
                        $item->autoPartBrand->only(['id', 'name']),
                        ['logo_url' => $item->autoPartBrand->logo_url]
                    )
                    : null,
                'auto_part_country' => $item->autoPartCountry
                    ? $item->autoPartCountry->only(['id', 'name'])
                    : null,
                'meta_title'        => $item->meta_title,
                'meta_description'  => $item->meta_description,
                'alt_text'          => $item->alt_text,
                'viscosity_grade'   => $item->viscosityGrade
                    ? $item->viscosityGrade->only(['id', 'name'])
                    : null,
            ]
        );

        if (! $withReviews) {
            return $base;
        }

        $ratingsBreakdown = $approvedReviews
            ->groupBy('rating')
            ->map(fn ($group) => $group->count())
            ->sortKeysDesc();

        return array_merge($base, [
            'feature_image'    => $item->auto_part_feature_image_url ?? null,
            'secondary_image'  => $item->auto_part_secondary_image_url ?? null,
            'gallery_images'   => $item->auto_part_gallery_urls ?? [],
            'total_reviews'    => $approvedReviews->count(),
            'ratings_breakdown'=> $ratingsBreakdown,
            'reviews'          => $approvedReviews->map(fn ($review) => [
                'name'       => $review->name,
                'summary'    => $review->summary,
                'review'     => $review->review,
                'rating'     => $review->rating,
                'created_at' => $review->created_at->diffForHumans(),
            ])->values(),
        ]);
    }


    public function searchByPartNumber(Request $request)
    {
        $request->validate(['part_number' => 'required|string']);
        $product = AutoPart::where('part_number', $request->part_number)->first();

        if (!$product) return response()->json(['message' => 'Not found'], 404);

        return response()->json(['data' => $product]);
    }

    public function findByCrossReference(Request $request)
    {
        $request->validate(['cross_reference' => 'required|string']);
        $products = AutoPart::whereJsonContains('cross_reference_numbers', $request->cross_reference)->get();

        if ($products->isEmpty()) return response()->json(['message' => 'No matches'], 404);

        return response()->json(['data' => $products]);
    }

    public function categoriesWithSub()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function brands()
    {
        $brands = AutoPartBrand::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $brands,
        ]);
    }

    public function countries()
    {
        $countries = AutoPartCountry::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $countries,
        ]);
    }

    public function filters()
    {
        // Get categories that have AutoParts (including subcategories)
        $categoryIds = AutoPart::whereNotNull('category_id')->distinct()->pluck('category_id');
        $parentIds = Category::whereIn('id', $categoryIds)->whereNotNull('parent_id')->pluck('parent_id');
        $allCategoryIds = $categoryIds->merge($parentIds)->unique();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => Category::with('children')->whereIn('id', $allCategoryIds)->whereNull('parent_id')->orderBy('name')->get(),
                'brands' => AutoPartBrand::select('id', 'name')->whereHas('autoParts')->orderBy('name')->get(),
                'countries' => AutoPartCountry::select('id', 'name')->whereHas('autoParts')->orderBy('name')->get(),
                'viscosity_grades' => ViscosityGrade::select('id', 'name')->whereHas('autoParts')->orderBy('name')->get(),
            ]
        ]);
    }
}
