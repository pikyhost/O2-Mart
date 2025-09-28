<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;

class LocationController extends Controller
{
    public function countries()
    {
        $countries = Country::active()->select(['id', 'name'])->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    public function governorates($countryId)
    {
        $governorates = Governorate::active()
            ->select(['id', 'name', 'is_active', 'country_id'])
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $governorates
        ]);
    }

    public function citiesByCountry($countryId)
    {
        $cities = \App\Models\City::whereHas('governorate', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->get(['id', 'name']);

        return response()->json($cities);
    }

    public function cities($governorateId)
    {
        $cities = City::active()
            ->select(['id', 'name', 'is_active', 'governorate_id'])
            ->where('governorate_id', $governorateId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    public function areas($cityId)
    {
        $areas = Area::active()
            ->select(['id', 'name', 'is_active', 'city_id'])
            ->where('city_id', $cityId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }
}
