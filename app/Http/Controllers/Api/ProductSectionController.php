<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductSection;
use Illuminate\Http\Request;

class ProductSectionController extends Controller
{
    public function show($type)
    {
        $section = ProductSection::where('type', $type)->first();

        if (!$section) {
            return response()->json([
                'status' => 'error',
                'message' => 'Section not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'background_image' => $section->background_image ? asset('storage/' . $section->background_image) : null,
                'section1_title'    => $section->section1_title,
                'section1_text1'    => $section->section1_text1,
                'section1_text2'    => $section->section1_text2,
                'section2_title'    => $section->section2_title,
                'section2_text'     => $section->section2_text,
                'seo' => [
                    'meta_title'       => $section->meta_title,
                    'meta_description' => $section->meta_description,
                    'alt_text'         => $section->alt_text,
                ],
            ],
        ]);
    }
}
