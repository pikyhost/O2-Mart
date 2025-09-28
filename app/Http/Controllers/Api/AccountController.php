<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Get the authenticated user's account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::guard('sanctum')->user();

        return response()->json([
            'first_name' => $this->extractFirstName($user->name),
            'last_name' => $this->extractLastName($user->name),
            'display_name' => $user->desc_for_comment,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);
    }

    /**
     * Update the authenticated user's account details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|required|string|max:50',
            'display_name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'country_id' => 'sometimes|nullable|exists:countries,id',
            'governorate_id' => 'sometimes|nullable|exists:governorates,id',
            'city_id' => 'sometimes|nullable|exists:cities,id',
            'area_id' => 'sometimes|nullable|exists:areas,id',
        ]);

        // Update name
        if ($request->has('first_name') || $request->has('last_name')) {
            $firstName = $request->input('first_name', $this->extractFirstName($user->name));
            $lastName = $request->input('last_name', $this->extractLastName($user->name));
            $user->name = trim("{$firstName} {$lastName}");
        }

        // Display name
        if ($request->has('display_name')) {
            $user->desc_for_comment = $validated['display_name'];
        }

        // Phone
        if ($request->has('phone')) {
            $user->phone = $validated['phone'];
        }

        // Location updates
        if ($request->has('country_id')) {
            $user->country_id = $validated['country_id'];
        }
        if ($request->has('governorate_id')) {
            $user->governorate_id = $validated['governorate_id'];
        }
        if ($request->has('city_id')) {
            $user->city_id = $validated['city_id'];
        }
        if ($request->has('area_id')) {
            $user->area_id = $validated['area_id'];
        }

        // Email with re-verification
        if ($request->has('email') && $request->input('email') !== $user->email) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->save();

            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Account updated successfully. Verification email sent to new address.',
                'user' => [
                    'first_name' => $this->extractFirstName($user->name),
                    'last_name' => $this->extractLastName($user->name),
                    'display_name' => $user->desc_for_comment,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'country_id' => $user->country_id,
                    'governorate_id' => $user->governorate_id,
                    'city_id' => $user->city_id,
                    'area_id' => $user->area_id,
                ]
            ]);
        }

        $user->save();

        return response()->json([
            'message' => 'Account updated successfully',
            'user' => [
                'first_name' => $this->extractFirstName($user->name),
                'last_name' => $this->extractLastName($user->name),
                'display_name' => $user->desc_for_comment,
                'phone' => $user->phone,
                'email' => $user->email,
                'country_id' => $user->country_id,
                'governorate_id' => $user->governorate_id,
                'city_id' => $user->city_id,
                'area_id' => $user->area_id,
            ]
        ]);
    }


    /**
     * Extract first name from full name
     */
    private function extractFirstName($name)
    {
        $parts = explode(' ', $name);
        return $parts[0] ?? '';
    }

    /**
     * Extract last name from full name
     */
    private function extractLastName($name)
    {
        $parts = explode(' ', $name);
        unset($parts[0]);
        return implode(' ', $parts);
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:sanctum'],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}
