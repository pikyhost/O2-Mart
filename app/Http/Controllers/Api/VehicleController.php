<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarMake;
use Illuminate\Http\Request;


class VehicleController extends Controller
{
    public function getCarMakes()
    {
        $makes = CarMake::where('is_active', true)
            ->select('id', 'name', 'slug', 'logo', 'country')
            ->orderBy('name')
            ->get();

        return response()->json(['status' => 'success', 'data' => $makes]);
    }
    public function getModelsByMake($id)
    {
        $make = CarMake::with(['models' => function ($query) {
            $query->select('id', 'car_make_id', 'name', 'slug', 'year_from', 'year_to', 'is_active')->orderBy('name');
        }])
        ->where('id', $id)
        ->where('is_active', true)
        ->select('id', 'name', 'slug', 'logo', 'country')
        ->first();

        if (!$make) {
            return response()->json(['status' => 'error', 'message' => 'Car Make not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $make]);
    }
    public function getModelYears($id)
    {
        $model = \App\Models\CarModel::where('id', $id)
            ->select('id', 'name', 'year_from', 'year_to')
            ->first();

        if (!$model) {
            return response()->json(['status' => 'error', 'message' => 'Car Model not found'], 404);
        }

        $years = range($model->year_from, $model->year_to);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $model->id,
                'name' => $model->name,
                'years' => $years,
            ]
        ]);
    }

    public function carMakesWithModelsAndYears()
    {
        $makes = CarMake::with(['models' => function ($query) {
            $query->select('id', 'car_make_id', 'name', 'slug', 'year_from', 'year_to', 'is_active')->orderBy('name');
        }])
        ->where('is_active', true)
        ->select('id', 'name', 'slug', 'logo', 'country')
        ->orderBy('name')
        ->get()
        ->map(function ($make) {
            $make->models = $make->models->filter(function ($model) {
                return $model->is_active;
            })->map(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'years' => range($model->year_from, $model->year_to),
                ];
            });
            return $make;
        });

        return response()->json([
            'status' => 'success',
            'data' => $makes,
        ]);
    }


private function getAttributeModel($type)
{
    return match ($type) {
        'battery' => \App\Models\BatteryAttribute::class,
        'tyre' => \App\Models\TyreAttribute::class,
        'rim' => \App\Models\RimAttribute::class,
        default => null,
    };
}

public function getCompatibleMakes($type)
{
    $model = $this->getAttributeModel($type);

    if (!$model) {
        return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
    }

    $makes = $model::with('make:id,name')
        ->select('car_make_id')
        ->distinct()
        ->get()
        ->pluck('make.name')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    return response()->json(['status' => 'success', 'data' => $makes]);
}

public function getCompatibleModels(Request $request, $type)
{
    $model = $this->getAttributeModel($type);

    if (!$model) {
        return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
    }

    $request->validate(['make' => 'required|string']);

    $models = $model::whereHas('make', fn($q) => $q->where('name', $request->make))
        ->with('model:id,name')
        ->select('car_model_id')
        ->distinct()
        ->get()
        ->pluck('model.name')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    return response()->json(['status' => 'success', 'data' => $models]);
}

public function getCompatibleYears(Request $request, $type)
{
    $model = $this->getAttributeModel($type);

    if (!$model) {
        return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
    }

    $request->validate([
        'make' => 'required|string',
        'model' => 'required|string',
    ]);

    $years = $model::whereHas('make', fn($q) => $q->where('name', $request->make))
        ->whereHas('model', fn($q) => $q->where('name', $request->model))
        ->select('model_year')
        ->distinct()
        ->pluck('model_year')
        ->filter()
        ->sort()
        ->values();

    return response()->json(['status' => 'success', 'data' => $years]);
}


}
