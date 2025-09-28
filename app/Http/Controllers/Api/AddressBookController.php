<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressBookController extends Controller
{
    /**
     * Get current user/session identifier
     */
    private function getCurrentIdentifier(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return [
                'user_id' => $user->id,
                'session_id' => null
            ];
        }

        abort(401, 'User must be authenticated');
    }

    /**
     * Get all addresses for current user/session
     */
    public function index(Request $request)
    {
        try {
            $identifier = $this->getCurrentIdentifier($request);

            $addresses = UserAddress::with(['country', 'governorate', 'city', 'area'])
                ->forUserOrSession($identifier['user_id'], $identifier['session_id'])
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $primaryAddress = $addresses->where('is_primary', true)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'primary_address' => $primaryAddress,
                    'addresses' => $addresses
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Get specific address
     */
    public function show(Request $request, $id)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $address = UserAddress::with(['country', 'governorate', 'city', 'area'])
            ->forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    /**
     * Create new address
     */
    public function store(Request $request)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $validator = Validator::make($request->all(), [
            'label' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($identifier) {
                    $exists = UserAddress::where(function($query) use ($identifier) {
                        if ($identifier['user_id']) {
                            $query->where('user_id', $identifier['user_id']);
                        } else {
                            $query->where('session_id', $identifier['session_id']);
                        }
                    })
                        ->where('label', $value)
                        ->exists();

                    if ($exists) {
                        $fail('You already have an address with this label.');
                    }
                }
            ],
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => [
                'required',
                'exists:governorates,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if governorate belongs to the selected country
                    $governorate = \App\Models\Governorate::where('id', $value)
                        ->where('country_id', $request->country_id)
                        ->first();

                    if (!$governorate) {
                        $fail('The selected governorate does not belong to the selected country.');
                    }
                }
            ],
            'city_id' => [
                'required',
                'exists:cities,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if city belongs to the selected governorate
                    $city = \App\Models\City::where('id', $value)
                        ->where('governorate_id', $request->governorate_id)
                        ->first();

                    if (!$city) {
                        $fail('The selected city does not belong to the selected governorate.');
                    }
                }
            ],
            'area_id' => [
                'nullable',
                'exists:areas,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        // Check if area belongs to the selected city
                        $area = \App\Models\Area::where('id', $value)
                            ->where('city_id', $request->city_id)
                            ->first();

                        if (!$area) {
                            $fail('The selected area does not belong to the selected city.');
                        }
                    }
                }
            ],
            'postal_code' => 'nullable|string|max:20',
            'additional_info' => 'nullable|string|max:1000',
            'is_primary' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $validatedData = array_merge($validatedData, $identifier);

        // Check if this is the first address, make it primary
        $existingCount = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])->count();
        if ($existingCount === 0) {
            $validatedData['is_primary'] = true;
        }

        $address = UserAddress::create($validatedData);
        $address->load(['country', 'governorate', 'city', 'area']);

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $address = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($identifier) {
                    $exists = UserAddress::where(function($query) use ($identifier) {
                        if ($identifier['user_id']) {
                            $query->where('user_id', $identifier['user_id']);
                        } else {
                            $query->where('session_id', $identifier['session_id']);
                        }
                    })
                        ->where('label', $value)
                        ->exists();

                    if ($exists) {
                        $fail('You already have an address with this label.');
                    }
                }
            ],
            'full_name' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address_line_1' => 'sometimes|string|max:500',
            'address_line_2' => 'sometimes|nullable|string|max:500',
            'country_id' => 'sometimes|exists:countries,id',
            'governorate_id' => 'sometimes|exists:governorates,id',
            'city_id' => 'sometimes|exists:cities,id',
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'additional_info' => 'sometimes|nullable|string|max:1000',
            'is_primary' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $address->update($validator->validated());
        $address->load(['country', 'governorate', 'city', 'area']);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    /**
     * Delete address
     */
    public function destroy(Request $request, $id)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $address = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->findOrFail($id);

        // Check if this is the only address
        $totalAddresses = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])->count();
        if ($totalAddresses === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the only address'
            ], 400);
        }

        // If deleting primary address, make another one primary
        if ($address->is_primary) {
            $nextAddress = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])
                ->where('id', '!=', $id)
                ->first();
            if ($nextAddress) {
                $nextAddress->update(['is_primary' => true]);
            }
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Make address primary
     */
    public function makePrimary(Request $request, $id)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $address = UserAddress::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->findOrFail($id);

        $address->update(['is_primary' => true]);
        $address->load(['country', 'governorate', 'city', 'area']);

        return response()->json([
            'success' => true,
            'message' => 'Address set as primary successfully',
            'data' => $address
        ]);
    }

    /**
     * Get primary address (for checkout)
     */
    public function getPrimary(Request $request)
    {
        $identifier = $this->getCurrentIdentifier($request);

        $primaryAddress = UserAddress::with(['country', 'governorate', 'city', 'area'])
            ->forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->primary()
            ->first();

        if (!$primaryAddress) {
            return response()->json([
                'success' => false,
                'message' => 'No primary address found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $primaryAddress
        ]);
    }

    /**
     * Transfer guest addresses to user account (when guest registers/logs in)
     */
    public function transferToUser(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $sessionId = $request->header('x-session-id') ?? session()->getId();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be authenticated'
            ], 401);
        }

        // Get guest addresses
        $guestAddresses = UserAddress::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        if ($guestAddresses->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No guest addresses to transfer',
                'transferred_count' => 0
            ]);
        }

        // Transfer addresses to user
        $transferredCount = 0;
        foreach ($guestAddresses as $address) {
            $address->update([
                'user_id' => $user->id,
                'session_id' => null
            ]);
            $transferredCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully transferred {$transferredCount} addresses to your account",
            'transferred_count' => $transferredCount
        ]);
    }
}
