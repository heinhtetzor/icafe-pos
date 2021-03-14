<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class WaiterAccountAuth
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
        if (!Auth::guard('waiter')->check()) {
            return redirect(route('waiter.showLogin'));
        }
        return $next($request);
    }
}
