<?php

namespace App\Providers;

use App\Livewire\ProfileContactDetails;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\Relation;
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
}
