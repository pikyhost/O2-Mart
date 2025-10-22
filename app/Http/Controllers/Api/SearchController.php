<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * Search endpoint for dropdowns
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:brands,categories,cities,areas,car_makes,car_models,countries,tyres,batteries,rims,auto_parts,mobile_vans,installation_centers,tyre_sizes,battery_brands,rim_brands,auto_part_brands',
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'filters' => 'nullable|array', // Additional filters like city_id for areas
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $request->type;
        $search = $request->search ?? '';
        $limit = $request->limit ?? 50;
        $filters = $request->filters ?? [];

        try {
            $results = $this->searchByType($type, $search, $limit, $filters);
            
            return response()->json([
                'status' => 'success',
                'type' => $type,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search by type
     */
    private function searchByType(string $type, string $search, int $limit, array $filters): array
    {
        return match ($type) {
            'brands' => $this->searchBrands($search, $limit),
            'categories' => $this->searchCategories($search, $limit),
            'cities' => $this->searchCities($search, $limit),
            'areas' => $this->searchAreas($search, $limit, $filters),
            'car_makes' => $this->searchCarMakes($search, $limit),
            'car_models' => $this->searchCarModels($search, $limit, $filters),
            'countries' => $this->searchCountries($search, $limit),
            'tyres' => $this->searchTyres($search, $limit),
            'batteries' => $this->searchBatteries($search, $limit),
            'rims' => $this->searchRims($search, $limit),
            'auto_parts' => $this->searchAutoParts($search, $limit),
            'mobile_vans' => $this->searchMobileVans($search, $limit),
            'installation_centers' => $this->searchInstallationCenters($search, $limit),
            'tyre_sizes' => $this->searchTyreSizes($search, $limit),
            'battery_brands' => $this->searchBatteryBrands($search, $limit),
            'rim_brands' => $this->searchRimBrands($search, $limit),
            'auto_part_brands' => $this->searchAutoPartBrands($search, $limit),
            default => []
        };
    }

    private function searchBrands(string $search, int $limit): array
    {
        $query = \App\Models\Brand::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id
            ])->toArray();
    }

    private function searchCategories(string $search, int $limit): array
    {
        $query = \App\Models\Category::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name', 'parent_id'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id,
                'parent_id' => $item->parent_id
            ])->toArray();
    }

    private function searchCities(string $search, int $limit): array
    {
        $query = \App\Models\City::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id
            ])->toArray();
    }

    private function searchAreas(string $search, int $limit, array $filters): array
    {
        $query = \App\Models\Area::with('city');
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Filter by city if provided
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }
        
        // Only active areas
        $query->where('is_active', true);
        
        return $query->limit($limit)
            ->get(['id', 'name', 'city_id', 'shipping_cost'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name . ($item->city ? ' - ' . $item->city->name : ''),
                'value' => $item->id,
                'city_id' => $item->city_id,
                'shipping_cost' => $item->shipping_cost
            ])->toArray();
    }

    private function searchCarMakes(string $search, int $limit): array
    {
        $query = \App\Models\CarMake::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id
            ])->toArray();
    }

    private function searchCarModels(string $search, int $limit, array $filters): array
    {
        $query = \App\Models\CarModel::with('carMake');
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Filter by make if provided
        if (!empty($filters['make_id'])) {
            $query->where('car_make_id', $filters['make_id']);
        }
        
        return $query->limit($limit)
            ->orderBy('name')
            ->get(['id', 'name', 'car_make_id'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name . ($item->carMake ? ' (' . $item->carMake->name . ')' : ''),
                'value' => $item->id,
                'make_id' => $item->car_make_id
            ])->toArray();
    }

    private function searchCountries(string $search, int $limit): array
    {
        $query = \App\Models\Country::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id
            ])->toArray();
    }

    private function searchTyres(string $search, int $limit): array
    {
        $query = \App\Models\Tyre::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name', 'regular_price'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id,
                'price' => $item->regular_price
            ])->toArray();
    }

    private function searchBatteries(string $search, int $limit): array
    {
        $query = \App\Models\Battery::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name', 'regular_price'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id,
                'price' => $item->regular_price
            ])->toArray();
    }

    private function searchRims(string $search, int $limit): array
    {
        $query = \App\Models\Rim::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'name', 'regular_price'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name,
                'value' => $item->id,
                'price' => $item->regular_price
            ])->toArray();
    }

    private function searchAutoParts(string $search, int $limit): array
    {
        $query = \App\Models\AutoPart::query();
        
        if (!empty($search)) {
            $query->where('product_name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'product_name', 'regular_price'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->product_name,
                'value' => $item->id,
                'price' => $item->regular_price
            ])->toArray();
    }

    private function searchMobileVans(string $search, int $limit): array
    {
        $query = \App\Models\MobileVanService::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        }
        
        return $query->where('is_active', true)
            ->limit($limit)
            ->get(['id', 'name', 'location'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name . ' - ' . $item->location,
                'value' => $item->id
            ])->toArray();
    }

    private function searchInstallationCenters(string $search, int $limit): array
    {
        $query = \App\Models\InstallerShop::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        }
        
        return $query->where('is_active', true)
            ->limit($limit)
            ->get(['id', 'name', 'location'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->name . ' - ' . $item->location,
                'value' => $item->id
            ])->toArray();
    }

    private function searchTyreSizes(string $search, int $limit): array
    {
        $query = \App\Models\TyreSize::query();
        
        if (!empty($search)) {
            $query->where('value', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'value'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->value,
                'value' => $item->id
            ])->toArray();
    }

    private function searchBatteryBrands(string $search, int $limit): array
    {
        $query = \App\Models\BatteryBrand::query();
        
        if (!empty($search)) {
            $query->where('value', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'value'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->value,
                'value' => $item->id
            ])->toArray();
    }

    private function searchRimBrands(string $search, int $limit): array
    {
        $query = \App\Models\RimBrand::query();
        
        if (!empty($search)) {
            $query->where('value', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'value'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->value,
                'value' => $item->id
            ])->toArray();
    }

    private function searchAutoPartBrands(string $search, int $limit): array
    {
        $query = \App\Models\AutoPartBrand::query();
        
        if (!empty($search)) {
            $query->where('value', 'like', "%{$search}%");
        }
        
        return $query->limit($limit)
            ->get(['id', 'value'])
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => $item->value,
                'value' => $item->id
            ])->toArray();
    }
}
