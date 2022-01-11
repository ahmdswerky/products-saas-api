<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
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
        $response = $next($request);

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' =>
                'Content-Type, X-Auth-Token, Host, X-API-Key, API-Key, X-Currency, X-Locale, Origin, Authorization'
        ];

        collect($headers)->keys()->map(function ($header) use ($headers, $response) {
            $response->header($header, $headers[$header]);
        });

        return $response;
    }
}
