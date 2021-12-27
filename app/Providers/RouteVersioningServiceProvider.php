<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class RouteVersioningServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
        $this->configureRateLimiting();

        $this->handleRouteModelBinding();

        $this->routes(function () {
            //Route::prefix('webhooks')
            //    ->middleware(['api'])
            //    ->namespace($this->namespace)
            //    ->group(base_path('routes/api/webhooks.php'));

            Route::prefix('webhooks')
                ->middleware(['api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/webhooks.php'));

            Route::prefix('api')
                ->middleware(['api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/guest.php'));

            Route::prefix('api')
                ->middleware(['api', 'auth:api,key'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/website.php'));

            Route::prefix('api/dashboard')
                ->middleware(['api', 'auth:api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/dashboard.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    public function handleRouteModelBinding()
    {
        Route::bind('payment', function ($value) {
            return Payment::where('public_id', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
        });

        Route::bind('product', function ($value) {
            return Product::where('slug', $value)
                ->orWhere('id', $value)
                ->orWhere('public_id', $value)
                ->firstOrFail();
        });

        Route::bind('user', function ($value) {
            return Product::where('public_id', $value)
                ->orWhere('id', $value)
                ->orWhere('public_id', $value)
                ->firstOrFail();
        });
    }
}
