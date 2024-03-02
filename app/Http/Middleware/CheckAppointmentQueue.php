<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAppointmentQueue
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
        if (
            !Auth::user()->Branch->BranchType->is_appointment &&
            !Auth::user()->Branch->BranchType->is_exhibition &&
            (
                Auth::user()->Branch->BranchType->is_direct_queue  &&
                Auth::user()->Branch->BranchConfiguration->layer === 1
            ) &&
            Auth::user()->role == 'cs'
        ) {
            $request->session()->flash('warning', __('Only Appointment Queue Branch can access this page'));
            return redirect(route('cs.home'));
        }

        if (
            !Auth::user()->Branch->BranchType->is_appointment &&
            !Auth::user()->Branch->BranchType->is_exhibition &&
            (
                Auth::user()->Branch->BranchType->is_direct_queue  &&
                Auth::user()->Branch->BranchConfiguration->layer === 1
            ) &&
            Auth::user()->role == 'admin_branch'
        ) {
            $request->session()->flash('warning', __('Only Appointment Queue Branch can access this page'));
            return redirect(route('admin-branch.dashboard'));
        }
        return $next($request);
    }
}
