<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteMacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::macro('api', function ($uri, $controller, $options = []) {
            $mapping = [
                'none' => [
                    'index' => 'get',
                    'store' => 'post',
                ],
                'id' => [
                    'show' => 'get',
                    'update' => 'post',
                    'destroy' => 'delete',
                ],
            ];

            collect($mapping)->map(function ($routes, $type) use ($uri, $controller) {
                $name = last(explode('/', $uri));
                $id = explode('/', $uri);
                $id = Str::singular(last($id));
                $id = "{{$id}}";

                if ($type === 'none') {
                    collect($routes)
                        ->map(
                            fn ($method, $route) => Route::{$method}($uri, [$controller, $route])
                                ->name(implode('.', [$name, $route]))
                        );
                } else {
                    collect($routes)
                        ->map(
                            fn ($method, $route) => Route::{$method}(implode('/', [$uri, $id]), [$controller, $route])
                                ->name(implode('.', [$name, $route]))
                        );
                }
            });
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
