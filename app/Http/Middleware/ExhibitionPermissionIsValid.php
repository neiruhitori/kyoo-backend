<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;

class ExhibitionPermissionIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->Branch->BranchType->is_exhibition && Auth::user()->role == 'admin_branch') {
            $request->session()->flash('warning', __('Only Exhibition Branch can access this page'));
            return redirect(route('adminBranch.home'));
        }

        if (!Auth::user()->Branch->BranchType->is_exhibition && Auth::user()->role == 'cs') {
            $request->session()->flash('warning', __('Only Exhibition Branch can access this page'));
            return redirect(route('cs.home'));
        }

        return $next($request);
    }
}
