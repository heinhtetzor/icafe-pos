<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminAccountAuth
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
        
        if (!Auth::guard('admin_account')->check()) {
            return redirect(route('admin.showLogin'));
        }
        return $next($request);
    }
}
