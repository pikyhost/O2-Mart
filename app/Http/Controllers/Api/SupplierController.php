<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'store_name' => $request->store_name,
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'Supplier created successfully.',
            'data' => $supplier,
        ], 201);
    }
}
