<?php

namespace App\Http\Middleware;

use App\Models\UserLoginToken;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class CheckAdminApiAuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (request()->has('token')) {
            try {
                $token = decrypt(request()->token, "DEG_FUCK");
                $tokenRecord = optional(UserLoginToken::with('user')->where('token', $token)->first());
                $user = $tokenRecord->user;
                if ($tokenRecord->is_login){
                    Filament::auth()->login($user);
                return redirect()->route('filament.admin.auth.login');
            } else {
                auth('web')->logout();
            }
            } catch (\Exception $e) {
              // return  403 page
                return abort(403, 'Unauthorized access. Invalid or expired token.');
            }
        }
        return $next($request);
    }
}
