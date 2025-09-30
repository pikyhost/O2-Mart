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
        $photoLink = $item->photo_link && str_starts_with($item->photo_link, 'http')
            ? $item->photo_link
            : ($item->photo_link ? asset('storage/' . $item->photo_link) : null);

        $approvedReviews = collect();
        if ($withReviews) {
            $approvedReviews = $item->relationLoaded('reviews')
                ? $item->reviews
                : $item->reviews()->where('is_approved', true)->latest()->get();
        }

        $average = $withReviews
            ? round(($approvedReviews->avg('rating') ?? 5), 2)
            : round(($item->average_rating ?? 5), 2);

        $base = array_merge(
            $item->toArray(),
            [
                'photo_link'        => $photoLink,
                'average_rating'    => $average,
                'auto_part_brand'   => $item->autoPartBrand
                    ? array_merge(
                        $item->autoPartBrand->only(['id', 'name']),
                        ['logo_url' => $item->autoPartBrand->logo_url]
                    )
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
        $brands = AutoPartBrand::all();

        return response()->json([
            'status' => 'success',
            'data' => $brands,
        ]);
    }

    public function countries()
    {
        $countries = AutoPartCountry::all();

        return response()->json([
            'status' => 'success',
            'data' => $countries,
        ]);
    }

    public function filters()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => Category::with('children')->whereNull('parent_id')->get(),
                'brands' => AutoPartBrand::withCount('autoParts')->get(),
                'countries' => AutoPartCountry::all(),
                'viscosity_grades' => ViscosityGrade::all(),
            ]
        ]);
    }
}
