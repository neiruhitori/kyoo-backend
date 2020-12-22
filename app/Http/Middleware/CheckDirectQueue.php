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
        if (!Auth::user()->Branch->BranchType->is_direct_queue) {
            $request->session()->flash('warning', 'Only Direct Queue Branch can access this page!');
            return redirect(route('adminBranch.home'));
        }
        return $next($request);
    }
}
