<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;

class ContactUsPageController extends Controller
{
    /**
     * Get the contact us page content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contactPage = ContactUs::first();

        if (!$contactPage) {
            return response()->json([
                'message' => 'Contact page content not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Contact page content retrieved successfully',
            'data' => array_merge($contactPage->toArray(), [
                'image_url' => $contactPage->image_url,
                'meta_title' => $contactPage->meta_title,
                'meta_description' => $contactPage->meta_description,
                'alt_text' => $contactPage->alt_text,
            ])
        ]);

    }
}
