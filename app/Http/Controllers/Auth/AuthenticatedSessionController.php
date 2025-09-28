<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserLoginToken;
use App\Services\CartService;
use App\Services\CompareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Exception;
use Illuminate\Support\Facades\Crypt;

/**
 * @group Authentication
 *
 * APIs for handling user authentication, including login and logout operations.
 */
class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $token = $user->currentAccessToken()?->plainTextToken ?? $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'User is already logged in',
                    'user' => $user,
                    'role' => $user->getRoleNames()->first(),
                    'token' => $token,
                    'redirect_url' => $this->getRedirectUrl($user, $request, $token),
                ], 409);
            }

            // Attempt login
            $request->authenticate();
            $user = Auth::guard('sanctum')->user();

            $sessionId = $request->header('x-session-id');
            
            CartService::mergeSessionCartWithUserCart($request->header('X-Session-ID'));
            CompareService::mergeSessionCompareWithUserCompare($request->header('X-Session-ID'));


            // Log in via session
            // Auth::guard('web')->login($user);

            // Revoke old tokens and issue new one
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            UserLoginToken::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $token,
                    'session_id' => $sessionId,
                    'is_login' => true,
                ]
            );

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user, $request, $token),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }


    protected function getRedirectUrl($user, Request $request, $token): string
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return route('filament.admin.auth.login', [
                'token' => Crypt::encryptString($token),
            ]);
        }

        return '/'; // Default fallback
    }

    public function destroy(Request $request)
    {
        try {
            foreach (['sanctum', 'web'] as $guard) {
                if (auth($guard)->check()) {
                    $user = auth($guard)->user();
                    $user->userLoginToken()->first()?->update([
                        'is_login' => false,
                        'session_id' => null,
                    ]);
                }
            }
            // Rate limit logout attempts
            $key = 'logout|' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'message' => 'Too many logout attempts. Please try again later.',
                ], 429);
            }

            // Check if user is authenticated via token
            $user = $request->user(); 

            if (!$user || !$user->currentAccessToken()) {
                return response()->json([
                    'message' => 'No active session or token found'
                ], 401);
            }

            // Revoke the current access token
            $user->currentAccessToken()->delete();

            
            // Rate limit hit
            RateLimiter::hit($key, 60); // 60 seconds

            // Return success response
            return response()->json([
                'message' => 'Logout successful',
                'user' => $user->only(['id', 'name', 'email'])
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}