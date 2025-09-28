<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class AuthenticateWithCookie
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $token = $request->cookie('auth_token');
        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated. No token found in cookies.',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json([
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        Auth::login($accessToken->tokenable);

        return $next($request);
    }
}
