<?php

namespace App\Providers;

use App\Helpers\ResourceRegistrar;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\PendingResourceRegistration;

class RouteMacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $self = $this;

        Route::macro('apiRoutes', function ($name, $controller, $options = []) use ($self) {
            $only = ['index', 'show', 'store', 'update', 'destroy'];

            if (isset($options['except'])) {
                $only = array_diff($only, (array) $options['except']);
            }

            return $self->resource($name, $controller, array_merge([
                'only' => $only,
            ], $options));
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

    public function resource($name, $controller, array $options = [])
    {
        $container = new Container;

        if ($container && $container->bound(ResourceRegistrar::class)) {
            $registrar = $container->make(ResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this->app['router']);
        }

        return new PendingResourceRegistration(
            $registrar,
            $name,
            $controller,
            $options
        );
    }
}
