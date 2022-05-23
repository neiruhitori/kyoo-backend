<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetClientIdCookie
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
        // Set cookie if not exists
        if (!$request->cookie('client_id')) {
            $expiry_time = intval((strtotime('tomorrow') - strtotime('now')) / 60);
            Cookie::queue('client_id', uniqid(), $expiry_time);
        }

        return $next($request);
    }
}
