<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PolicyController extends Controller
{
    public function privacy(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->privacy_policy;

        return response()->json([
            'title' => 'Privacy Policy',
            'markdown' => $markdown,
            'html' => Str::markdown($markdown ?? ''),
            'meta_title' => $policy?->meta_title_privacy_policy,
            'meta_description' => $policy?->meta_description_privacy_policy,
            'alt_text' => $policy?->alt_text_privacy_policy,
        ]);
    }

    public function refund(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->refund_policy;

        return response()->json([
            'title' => 'Warranty & Returns Policy',
            'markdown' => $markdown,
            'html' => Str::markdown($markdown ?? ''),
            'meta_title' => $policy?->meta_title_refund_policy,
            'meta_description' => $policy?->meta_description_refund_policy,
            'alt_text' => $policy?->alt_text_refund_policy,
        ]);
    }

    public function terms(): JsonResponse
    {
        $policy = Policy::first();
        $markdown = $policy?->terms_of_service;

        return response()->json([
            'title' => 'Terms of Service',
            'markdown' => $markdown,
            'html' => Str::markdown($markdown ?? ''),
            'meta_title' => $policy?->meta_title_terms_of_service,
            'meta_description' => $policy?->meta_description_terms_of_service,
            'alt_text' => $policy?->alt_text_terms_of_service,
        ]);
    }
}
