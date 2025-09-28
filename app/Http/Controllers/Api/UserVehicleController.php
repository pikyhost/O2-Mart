<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserVehicle;
use Illuminate\Http\Request;

class UserVehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles()
            ->with(['make:id,name', 'model:id,name'])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'car_make_id' => $vehicle->car_make_id,
                    'car_model_id' => $vehicle->car_model_id,
                    'car_year' => $vehicle->car_year,
                    'vin' => $vehicle->vin,
                    'mileage' => $vehicle->mileage,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'created_at' => $vehicle->created_at,
                    'updated_at' => $vehicle->updated_at,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $vehicles]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_make_id' => 'required|exists:car_makes,id',
            'car_model_id' => 'required|exists:car_models,id',
            'car_year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'vin' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
        ]);

        $vehicle = auth()->user()->vehicles()->create($data);
        $vehicle->load(['make:id,name', 'model:id,name']);

        return response()->json(['status' => 'success', 'data' => $vehicle]);
    }

    public function update(Request $request, $id)
    {
        $vehicle = auth()->user()->vehicles()->findOrFail($id);

        $data = $request->validate([
            'car_make_id' => 'sometimes|exists:car_makes,id',
            'car_model_id' => 'sometimes|exists:car_models,id',
            'car_year' => 'sometimes|digits:4|integer|min:1900|max:' . date('Y'),
            'vin' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
        ]);

        $vehicle->update($data);
        $vehicle->load(['make:id,name', 'model:id,name']);

        return response()->json(['status' => 'success', 'data' => $vehicle]);
    }

    public function destroy($id)
    {
        $vehicle = auth()->user()->vehicles()->findOrFail($id);
        $vehicle->delete();

        return response()->json(['status' => 'success', 'message' => 'Vehicle removed']);
    }
}

