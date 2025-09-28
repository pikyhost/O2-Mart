<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthOrSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
            \Log::info('🔍 AUTH DEBUG', [
        'sanctum_check' => auth('sanctum')->check(),
        'user' => auth('sanctum')->user(),
        'token' => $request->bearerToken(),
        'session_id' => $request->header('x-session-id'),
    ]);
        if (auth('sanctum')->check()) {
            return $next($request);
        }

        $sessionId = $request->header('x-session-id');

        if ($sessionId) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated or session ID missing'
        ], 401);
    }

}
