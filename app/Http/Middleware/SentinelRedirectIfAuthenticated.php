<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Sentinel::check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
