<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAdminBranchPassword
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
        if (!Auth::user()->is_password_changed) {
            return redirect(route('adminBranch.profile.edit'));
        }
        return $next($request);
    }
}
