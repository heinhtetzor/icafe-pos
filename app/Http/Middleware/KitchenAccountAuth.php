<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class KitchenAccountAuth
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
        if (!Auth::guard('kitchen')->check()) {
            return redirect(route('kitchen.showLogin'));
        }
        return $next($request);
    }
}
