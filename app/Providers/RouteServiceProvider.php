<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

final class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Default web rate limiter
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Custom transfer rate limiter
        RateLimiter::for('transfers', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->input('sender_account_id'))
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many transfer attempts. Please try again later.',
                        'retry_after' => 60,
                    ], 429);
                });
        });
    }
} 