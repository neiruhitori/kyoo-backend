<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SetlocaleByIP
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

        $ip = $request->getClientIp();
         //handle local env
         if ($ip == '127.0.0.1' || $ip == '::1') {
            $ip = '8.8.8.8'; // example en/us
            // $ip = '36.74.200.91'; // example id
        }

        $cacheKey = 'ip_country_' . $ip;
    
        // Cek cache terlebih dahulu
        if (Cache::has($cacheKey)) {
            // dd(Cache::get($cacheKey));
            $country = Cache::get($cacheKey);
            // Log::info('Cache found, country: '. $country);
            $locale = ($country == 'ID') ? 'id' : 'en';
            app()->setLocale($locale);
            session(['locale' => $locale]);
            
            return $next($request);
        }
       

        try {
            $response = Http::get("https://ipinfo.io/{$ip}/json");
            if ($response->successful()) {
                $data = $response->json();
                
                // check location
                // if (isset($data['country']) && $data['country'] !== 'ID') {
                    $country = $data['country'] ?? 'ID';
                // }
                Log::info('Request hit, country: '. $country);

            Cache::put($cacheKey, $country, now()->addDays(1));

            }

        } catch (\Exception $e) {
            $browserLang = request()->getPreferredLanguage();
            $country = strpos($browserLang, 'id') !== false ? 'ID' : 'EN';
        }
        $locale = ($country == 'ID') ? 'id' : 'en';
        // Set locale
        app()->setLocale($locale);
        session(['locale' => $locale]);
        
        return $next($request);
    }
}
