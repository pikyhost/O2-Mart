<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\EmailUpdateNotification;
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
        
        // Load addresses with relationships
        $addresses = $user->addressBooks()
            ->with(['country', 'governorate', 'city', 'area'])
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'user_id' => $address->user_id,
                    'session_id' => $address->session_id,
                    'label' => $address->label,
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'country_id' => $address->country_id,
                    'governorate_id' => $address->governorate_id,
                    'city_id' => $address->city_id,
                    'area_id' => $address->area_id,
                    'postal_code' => $address->postal_code,
                    'additional_info' => $address->additional_info,
                    'is_primary' => $address->is_primary,
                    'created_at' => $address->created_at,
                    'updated_at' => $address->updated_at,
                    'house_no' => $address->house_no,
                    'building_name' => $address->building_name,
                    'landmark' => $address->landmark,
                    'area_text' => $address->area_text,
                    'full_location' => $address->full_location,
                    'country' => $address->country,
                    'governorate' => $address->governorate,
                    'city' => $address->city,
                    'area' => $address->area,
                ];
            });
        
        $primaryAddress = $addresses->firstWhere('is_primary', true);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'first_name' => $this->extractFirstName($user->name),
                    'last_name' => $this->extractLastName($user->name),
                    'display_name' => $user->desc_for_comment,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ],
                'primary_address' => $primaryAddress,
                'addresses' => $addresses,
            ]
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
            $user->notify(new EmailUpdateNotification());
            
            // Reload addresses
            $addresses = $user->addressBooks()->with(['country', 'governorate', 'city', 'area'])->get();
            $primaryAddress = $addresses->firstWhere('is_primary', true);

            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully. Verification email sent to new address.',
                'data' => [
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
                    ],
                    'primary_address' => $primaryAddress,
                    'addresses' => $addresses,
                ]
            ]);
        }

        $user->save();
        
        // Reload addresses
        $addresses = $user->addressBooks()->with(['country', 'governorate', 'city', 'area'])->get();
        $primaryAddress = $addresses->firstWhere('is_primary', true);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully',
            'data' => [
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
                ],
                'primary_address' => $primaryAddress,
                'addresses' => $addresses,
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
