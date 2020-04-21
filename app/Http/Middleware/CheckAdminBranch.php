<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAdminBranch
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
        if (Auth::user()->role != 'admin_branch') {
            return redirect(route('unauthorized'));
        }
        return $next($request);
    }
}
