<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AntiScrapingMiddleware
{
    private $blockedUserAgents = [
        'python-requests', 'curl', 'scrapy', 'wget', 'httpie', 'postman',
        'insomnia', 'bot', 'crawler', 'spider', 'scraper'
    ];

    private $allowedOrigins = [
        'https://mk3bel.o2mart.net',
        'https://o2mart.net',
        'http://localhost:3000',
        'http://127.0.0.1:3000'
    ];

    public function handle(Request $request, Closure $next)
    {
        // Block suspicious User-Agents
        $userAgent = strtolower($request->userAgent() ?? '');
        if (empty($userAgent) || $this->isBlockedUserAgent($userAgent)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check Origin/Referer for API requests
        if ($request->is('api/*')) {
            $origin = $request->header('Origin');
            $referer = $request->header('Referer');
            
            if (!$this->isValidOrigin($origin, $referer)) {
                return response()->json(['error' => 'Invalid origin'], 403);
            }
        }

        return $next($request);
    }

    private function isBlockedUserAgent(string $userAgent): bool
    {
        foreach ($this->blockedUserAgents as $blocked) {
            if (str_contains($userAgent, $blocked)) {
                return true;
            }
        }
        return false;
    }

    private function isValidOrigin(?string $origin, ?string $referer): bool
    {
        if (!$origin && !$referer) return false;
        
        foreach ($this->allowedOrigins as $allowed) {
            if (str_starts_with($origin ?? '', $allowed) || str_starts_with($referer ?? '', $allowed)) {
                return true;
            }
        }
        return false;
    }
}