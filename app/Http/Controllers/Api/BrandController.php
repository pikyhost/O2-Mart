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
        return response()->json(Brand::active()->get());
    }

    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        return response()->json($brand);
    }

    public function all()
    {
        return response()->json(Brand::all());
    }

    public function allByType(Request $request)
    {
        $request->validate([
            'type' => 'required|in:auto_part,tyre,battery,rim',
        ]);

        $type = $request->type;

        $brands = match ($type) {
            'auto_part' => AutoPartBrand::all()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'logo_url' => $b->logo_url,
            ]),
            'tyre' => TyreBrand::all()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'logo_url' => $b->logo_url,
            ]),
            'battery' => BatteryBrand::all()->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->value,
                'logo_url' => $b->logo_url,
            ]),
            'rim' => RimBrand::all()->map(fn($b) => [
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
        $brands = Brand::active()->get();
        return response()->json([
            'status' => true,
            'data' => $brands,
        ]);
    }
}
