<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function setLang($locale)
    {
        if (!in_array($locale, ['en', 'id'])) {
            abort(404, 'Bahasa tidak didukung');
        }
    
        session()->put('locale', $locale);
        app()->setLocale($locale);
    
        return redirect()->back();
    }
}
