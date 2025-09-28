<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Governorate;
use App\Models\City;
use App\Models\UserLoginToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Exception;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already logged in.',
            ], 403);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string'],
            'second_phone' => ['nullable', 'string'],
            'preferred_language' => ['nullable', 'string', 'max:5'],
            'avatar_url' => ['nullable', 'url'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

        // Validate relationships
        if ($request->country_id && $request->governorate_id) {
            $isValidGov = Governorate::where('id', $request->governorate_id)
                ->where('country_id', $request->country_id)
                ->exists();

            if (!$isValidGov) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_governorate'),
                ], 400);
            }
        }

        if ($request->governorate_id && $request->city_id) {
            $isValidCity = City::where('id', $request->city_id)
                ->where('governorate_id', $request->governorate_id)
                ->exists();

            if (!$isValidCity) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_city'),
                ], 400);
            }
        }

        // Create user
        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'second_phone' => $request->second_phone,
            'preferred_language' => $request->preferred_language ?? 'en',
            'avatar_url' => $request->avatar_url,
            'country_id' => $request->country_id,
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'is_active' => true,
        ]);

        $user->assignRole('client');

        // event(new Registered($user));

        Auth::login($user);

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        try {
            UserLoginToken::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $token,
                    'session_id' => null,
                    'is_login' => true,
                ]
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to store session.',
                'error' => $e->getMessage(),
            ], 500);
        }

    $fullName = $user->name;
    $names = explode(' ', $fullName, 2);
    $firstName = $names[0];
    $lastName = isset($names[1]) ? $names[1] : '';
    
    return response()->json([
        'status' => 'success',
        'message' => __('auth.registration_success'),
        'user' => array_merge(
            $user->only([
                'id', 'name', 'email', 'phone', 'second_phone', 'preferred_language',
                'avatar_url', 'country_id', 'governorate_id', 'city_id', 'is_active', 'created_at'
            ]),
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]
        ),
        'role' => $user->getRoleNames()->first(),
        'token' => $token,
        'redirect_url' => $this->getRedirectUrl($user, $request, $token),
    ], 201)
        ->cookie(
            'auth_token', $token, 60 * 24 * 30, '/', null, false, false, false, 'Lax'
        )
        ->withCookie(
            cookie('XSRF-TOKEN', csrf_token(), 0, '/', null, true, true, false, 'strict')
        );

    }

    protected function getRedirectUrl($user, Request $request, $token): string
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return route('filament.admin.auth.login', [
                'token' => Crypt::encryptString($token),
            ]);
        }

//        if ($user->hasRole('client')) {
//            return route('filament.client.auth.login', [
//                'token' => Crypt::encryptString($token),
//            ]);
//        }

        return '/';
    }
}
