<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // ── Rate limit: Absensi ──
        // Max 10 submission attempts per authenticated user per day.
        // Falls back to IP if unauthenticated (should never happen, route is auth-guarded).
        RateLimiter::for('absensi', function (Request $request) {
            return Limit::perDay(10)->by($request->user()?->id ?: $request->ip());
        });
        
        if (str_contains(config('app.url'), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
    }
}
