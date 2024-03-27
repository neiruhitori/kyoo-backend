<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;

class SetTimeZone
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
        if (Auth::check()) {
            $user = Auth::user();
            $branchTimeZone = $user->branch->timezone;

            if ($branchTimeZone == 'WITA') {
                config(['app.timezone' => 'Asia/Makassar']);
            } else if ($branchTimeZone == 'WIT') {
                config(['app.timezone' => 'Asia/Jayapura']);
            } else {
                config(['app.timezone' => 'Asia/Jakarta']);
            }

            date_default_timezone_set(config('app.timezone'));
        }

        return $next($request);
    }
}
