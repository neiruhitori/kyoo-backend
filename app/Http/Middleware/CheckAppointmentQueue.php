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
        if (!Auth::user()->Branch->BranchType->is_appointment && Auth::user()->role == 'cs') {
            $request->session()->flash('warning', 'Only Appointment Queue Branch can access this page!');
            return redirect(route('cs.home'));
        }
        if (!Auth::user()->Branch->BranchType->is_appointment && Auth::user()->role == 'admin_branch') {
            $request->session()->flash('warning', 'Only Appointment Queue Branch can access this page!');
            return redirect(route('adminBranch.home'));
        }
        return $next($request);
    }
}
