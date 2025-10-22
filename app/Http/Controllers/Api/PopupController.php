<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Models\Coupon;
use App\Models\PopupEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\CouponEmail;

class PopupController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Fetch active popups, ordered by popup_order
            $popups = Popup::active()->orderBy('popup_order')->get();

            // Check if popups are empty
            if ($popups->isEmpty()) {
                return response()->json([
                    'error' => 'No active popups found',
                    // 'support_link' => route('contact.us'),
                ], 404);
            }

            // Load frontend pages mapping
            $pageMap = config('frontend-pages');

            // Format popups with image URLs and friendly specific pages
            $formattedPopups = $popups->map(function ($popup) use ($pageMap) {
                $specificPagesRaw = json_decode($popup->specific_pages ?? '[]', true);

                $specificPagesFriendly = collect($specificPagesRaw)
                    ->map(fn ($uri) => [
                        'uri' => $uri,
                        'label' => $pageMap[$uri] ?? $uri,
                    ])
                    ->values()
                    ->toArray();

                return [
                    'id' => $popup->id,
                    'title' => $popup->title,
                    'description' => $popup->description,
                    'image_path' => $popup->image_path ? asset('storage/' . $popup->image_path) : null,
                    'cta_text' => $popup->cta_text,
                    'cta_link' => $popup->cta_link,
                    'offer_title' => $popup->offer_title,
                    'offer_type' => $popup->offer_type,
                    'is_active' => $popup->is_active,
                    'email_needed' => $popup->email_needed,
                    'display_rules' => $popup->display_rules,
                    'popup_order' => $popup->popup_order,
                    'show_interval_minutes' => $popup->show_interval_minutes,
                    'delay_seconds' => $popup->delay_seconds,
                    'duration_seconds' => $popup->duration_seconds,
                    'dont_show_again_days' => $popup->dont_show_again_days,
                    'specific_pages' => $specificPagesFriendly,
                    'coupon' => $popup->coupon ? [
                        'id' => $popup->coupon->id,
                        'code' => $popup->coupon->code,
                        'type' => $popup->coupon->type,
                        'value' => $popup->coupon->value,
                    ] : null,
                    'created_at' => $popup->created_at->toIso8601String(),
                    'updated_at' => $popup->updated_at->toIso8601String(),
                ];
            })->toArray();

            return response()->json([
                'data' => $formattedPopups,
                'message' => 'Active popups retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Popup Index Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Unable to load popups at this time. Please refresh the page and try again.',
                'message' => 'If the problem persists, please contact support.',
            ], 500);
        }
    }

    /**
     * Handle email submission for popups that require email and send coupon
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function submitEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'popup_id' => 'required|exists:popups,id',
            'email' => 'required|email',
        ], [
            'popup_id.required' => 'Popup ID is required',
            'popup_id.exists' => 'Invalid popup selected',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Please check your input and try again',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $popup = Popup::find($request->popup_id);

            // Get an active coupon 
            $coupon = $popup->coupon;

            if (!$coupon) {
                return response()->json([
                    'error' => 'No coupon is associated with this offer',
                    'message' => 'This promotion is currently unavailable.',
                ], 404);
            }

            if (!$coupon->is_active) {
                return response()->json([
                    'error' => 'This coupon is no longer active',
                    'message' => 'This promotion has ended.',
                ], 404);
            }

            if ($coupon->expires_at && $coupon->expires_at < now()) {
                return response()->json([
                    'error' => 'This coupon has expired',
                    'message' => 'This promotion has ended.',
                ], 404);
            }

            // Save email submission
            PopupEmail::create([
                'popup_id' => $popup->id,
                'email' => $request->email,
            ]);

            // Send email with coupon
            Mail::to($request->email)->send(new CouponEmail($coupon));

            return response()->json([
                'message' => 'Coupon code has been sent to your email',
                'coupon_code' => $coupon->code,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Popup Email Submission Error', [
                'popup_id' => $request->popup_id,
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Unable to send coupon email',
                'message' => 'There was a problem sending your coupon. Please try again or contact support if the issue persists.',
            ], 500);
        }

    }
}
