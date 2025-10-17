<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoPartBrand;
use App\Models\BatteryBrand;
use App\Models\Brand;
use App\Models\RimBrand;
use App\Models\TyreBrand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $appends = ['logo_url'];

    public function index()
    {
        return response()->json(Brand::active()->orderBy('name')->get());
    }

    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        return response()->json($brand);
    }

    public function all()
    {
        return response()->json(Brand::orderBy('name')->get());
    }

    public function allByType(Request $request)
    {
        $request->validate([
            'type' => 'required|in:auto_part,tyre,battery,rim',
        ]);

        $type = $request->type;

        $brands = match ($type) {
            'auto_part' => AutoPartBrand::orderBy('name')->get()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'logo_url' => $b->logo_url,
            ]),
            'tyre' => TyreBrand::orderBy('name')->get()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'logo_url' => $b->logo_url,
            ]),
            'battery' => BatteryBrand::orderBy('value')->get()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->value,
                'logo_url' => $b->logo_url,
            ]),
            'rim' => RimBrand::orderBy('name')->get()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'logo_url' => $b->logo_url,
            ]),
            default => [],
            };

            
            return response()->json([
                'status' => 'success',
                'data' => $brands,
            ]);
        }

    public function active()
    {
        $brands = Brand::active()->orderBy('name')->get();
        return response()->json([
            'status' => true,
            'data' => $brands,
        ]);
    }
}
