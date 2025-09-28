<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleGuestSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure session is started
        if (!$request->hasSession()) {
            $request->setLaravelSession(app('session')->driver());
        }

        // Start session if not started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        return $next($request);
    }
}
