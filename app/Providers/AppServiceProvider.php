<?php

namespace App\Providers;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('logins', function () {
            return Limit::perMinute(5)->by(request()->input('email'));
        });
        RateLimiter::for('cart', function () {
            return Limit::perMinute(10)->by(request()->user()->id);
        });
        RateLimiter::for('orders', function () {
            return Limit::perMinute(3)->by(request()->user()->id);
        });
        RateLimiter::for('reports', function () {
            return Limit::perMinute(2)->by(request()->user()->id);
        });
    }
}
