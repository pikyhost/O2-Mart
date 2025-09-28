<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CarModel;
use App\Models\VinRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'country'])->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function show($id): JsonResponse
    {
        $product = Product::with(['categories', 'attributes'])->findOrFail($id);

        $primaryCategory = $product->categories->firstWhere('pivot.is_primary', true);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->regular_price,
            'category' => $primaryCategory?->name,
            'attributes' => $product->attributes->map(function ($attr) {
                return [
                    'name' => $attr->name,
                    'type' => $attr->type,
                    'value' => $attr->pivot->value,
                ];
            }),
        ]);
    }

    public function filter(Request $request)
    {
        $query = Product::query()->with(['categories', 'brand', 'attributes', 'compatibleCarModels']);

        // Filter by category attributes
        if ($request->filled('attributes')) {
            foreach ($request->input('attributes') as $attrId => $value) {
                $query->whereHas('attributes', function ($q) use ($attrId, $value) {
                    $q->where('attribute_id', $attrId)
                    ->where('value', $value);
                });
            }
        }

        //  Filter by visual attributes 
        if ($request->filled('tyre_type')) {
            $query->whereHas('attributes', function ($q) use ($request) {
                $q->where('name', 'tyre_type')->where('value', $request->tyre_type);
            });
        }

        //  Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by availability
        if ($request->filled('available')) {
            $query->where('is_active', filter_var($request->available, FILTER_VALIDATE_BOOLEAN));
        }


        // Vehicle compatibility
        if ($request->filled(['make', 'model', 'year'])) {
            $query->whereHas('compatibleCarModels', function ($q) use ($request) {
                $q->whereHas('make', function ($subQ) use ($request) {
                    $subQ->where('name', $request->make);
                })
                ->where('name', $request->model)
                ->where('product_car_compatibility.year_from', '<=', $request->year)
                ->where(function ($q) use ($request) {
                    $q->whereNull('product_car_compatibility.year_to')
                    ->orWhere('product_car_compatibility.year_to', '>=', $request->year);
                });
            });
        }


        // Save preferences if user is logged in
        if (auth('sanctum')->check() && $request->boolean('save_filter')) {
            $filterKeys = [
                'attributes', 'tyre_type', 'price_min', 'price_max',
                'brand_id', 'available', 'make', 'model', 'year'
            ];

            $filters = collect($request->only($filterKeys))->filter();
            auth()->user()->update(['last_filter' => $filters]);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    public function getCompatibleVehicles($productId)
    {   
        $product = \App\Models\Product::findOrFail($productId);

        return response()->json([
            'data' => $product->compatibleCarModels()->with('make')->get()
        ]);
    }


    public function checkCompatibility(Request $request)
    {
        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (now()->year + 1),
        ]);

        $carModel = CarModel::whereHas('make', fn($query) => $query->where('name', $request->make))
            ->where('name', $request->model)
            ->where('year_from', '<=', $request->year)
            ->where(function ($query) use ($request) {
                $query->whereNull('year_to')->orWhere('year_to', '>=', $request->year);
            })->first();

        if (!$carModel) {
            return response()->json(['compatible' => false, 'message' => 'Vehicle not found'], 404);
        }

        $products = $carModel->compatibleProducts()->get();

        return response()->json([
            'compatible' => true,
            'products' => $products,
        ]);
    }

    public function getRecommendations(Request $request)
    {
        $request->validate([
            'vin' => 'required|string|size:17',
        ]);

        $record = VinRecord::where('vin', $request->vin)->first();

        if (!$record) {
            return response()->json(['message' => 'VIN not found'], 404);
        }

        $products = $record->carModel->compatibleProducts()->get();

        return response()->json([
            'vin' => $record->vin,
            'car_model' => [
                'make' => $record->carModel->make->name ?? null,
                'model' => $record->carModel->name,
                'year_from' => $record->carModel->year_from,
                'year_to' => $record->carModel->year_to,
            ],
            'products' => $products,
        ]);
    }
    public function searchByPartNumber(Request $request): JsonResponse
    {
        $request->validate([
            'part_number' => 'required|string',
        ]);

        $product = Product::where('part_number', $request->part_number)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product]);
    }

    public function findByCrossReference(Request $request): JsonResponse
    {
        $request->validate([
            'cross_reference' => 'required|string',
        ]);

        $products = Product::whereJsonContains('cross_reference_numbers', $request->cross_reference)->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No matching products found'], 404);
        }

        return response()->json(['data' => $products]);
    }


}
