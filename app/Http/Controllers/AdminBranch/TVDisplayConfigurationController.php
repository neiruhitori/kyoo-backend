<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TVDisplayConfigurationController extends Controller
{
    public function index()
    {
        return view('adminBranch.tvDisplayConfiguration');
    }
}
