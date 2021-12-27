<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class Translation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($locale = $request->header('X-Locale')) {
            setlocale(LC_ALL, $locale);
            App::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
