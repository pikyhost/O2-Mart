<?php

namespace App\Providers;

use App\Livewire\ProfileContactDetails;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Rate Limiters
        $this->configureRateLimiting();
        
        // Force HTTPS for ngrok
        if (str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
        }
        
        // Set password validation rules
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->symbols();
        });
        
        ProfileContactDetails::setSort(10);

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            $email = $notifiable->getEmailForPasswordReset();
            return route('password.reset.link', ['token' => $token, 'email' => $email]);
        });

        Gate::before(function ($user, $ability) {
            if ($user->hasRole(['admin', 'super_admin'])) {
                return true;
            }
        });

            Relation::morphMap([
                'tyre' => \App\Models\Tyre::class,
                'rim' => \App\Models\Rim::class,
                'battery' => \App\Models\Battery::class,
                'auto_part' => \App\Models\AutoPart::class,
            ]);

    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter - 60 requests per minute per IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Authenticated API - 120 requests per minute per user
        RateLimiter::for('api-authenticated', function (Request $request) {
            return auth('sanctum')->check()
                ? Limit::perMinute(70)->by(auth('sanctum')->id())
                : Limit::perMinute(60)->by($request->ip());
        });

        // Sensitive operations (login, register, password reset) - 5 requests per minute
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many attempts. Please try again later.',
                    ], 429, $headers);
                });
        });

        // Contact/Inquiry forms - 10 requests per minute
        RateLimiter::for('forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many form submissions. Please try again later.',
                    ], 429, $headers);
                });
        });

        // Checkout operations - 10 requests per minute
        RateLimiter::for('checkout', function (Request $request) {
            return auth('sanctum')->check()
                ? Limit::perMinute(10)->by(auth('sanctum')->id())
                : Limit::perMinute(5)->by($request->ip());
        });

        // Search operations - 30 requests per minute
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Cart operations - 60 requests per minute
        RateLimiter::for('cart', function (Request $request) {
            return auth('sanctum')->check()
                ? Limit::perMinute(60)->by(auth('sanctum')->id())
                : Limit::perMinute(60)->by($request->ip());
        });
    }
}
