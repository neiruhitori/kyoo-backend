<?php

namespace App\Http\Controllers\AdminBranch;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeatureController extends Controller
{
    public function index()
    {
        return view('adminBranch.feature');
    }
}
