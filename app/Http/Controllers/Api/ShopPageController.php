<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopPage;

class ShopPageController extends Controller
{
    public function index()
    {
        $page = ShopPage::first();

        return response()->json([
            'section_1' => [
                'title' => $page->section_1_title,
                'content' => $page->section_1_content,
            ],
            'section_2' => [
                'title' => $page->section_2_title,
                'content' => $page->section_2_content,
                'image' => $page->section_2_image ? asset('storage/' . $page->section_2_image) : null,

            ],
            'seo' => [
                'meta_title'       => $page->meta_title,
                'meta_description' => $page->meta_description,
                'alt_text'         => $page->alt_text,
            ]
        ]);
    }
}
