<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetlocaleByHost
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

        $domainLocaleMap = [
            'dev.kyoo.id' => 'id',
            'worldwide.kyoo.id' => 'en',
        ];

        $host = $request->getHost();
        $locale = $domainLocaleMap[$host];
         dd($locale);

        // // Jika domain tidak ada di daftar, redirect ke domain default
        // if (!array_key_exists($host, $domainLocaleMap)) {
        //     return redirect('https://dev.kyoo.id');
        // }
        // $locale = $domainLocaleMap[$host];
        // app()->setLocale($locale);

        // return $next($request);
    }
}
