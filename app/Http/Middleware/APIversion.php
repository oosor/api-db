<?php

namespace App\Http\Middleware;

use Closure;

class APIversion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard)
    {
        config(['app.api_version' => $guard]);
        return $next($request);
    }
}
