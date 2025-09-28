<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('attributes')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'attributes' => $category->attributes->map(function ($attr) {
                    return [
                        'name' => $attr->name,
                        'type' => $attr->type,
                        'required' => $attr->pivot->is_required,
                    ];
                }),
            ];
        });

        return response()->json($categories);
    }
}
