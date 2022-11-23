<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('adminCorporate.monitoring');
    }
}
