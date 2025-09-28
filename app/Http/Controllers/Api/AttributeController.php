<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index()
    {
        return response()->json(
            Attribute::all()->map(function ($attr) {
                return [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'type' => $attr->type,
                    'options' => $attr->options,
                ];
            })
        );
    }
}
