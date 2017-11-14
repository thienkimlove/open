<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class Acl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();

        if (! $user) {
            return redirect()->guest('login');
        }

        return $next($request);
    }
}
