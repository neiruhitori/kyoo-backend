<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckDirectQueue
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
        if (!Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->role == 'admin_branch') {
            $request->session()->flash('warning', __('Only Direct Queue Branch can access this page'));
            return redirect(route('admin-branch.dashboard'));
        }
        if (!Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->role == 'cs') {
            $request->session()->flash('warning', __('Only Direct Queue Branch can access this page'));
            return redirect(route('cs.home'));
        }
        return $next($request);
    }
}
