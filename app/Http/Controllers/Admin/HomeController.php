<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;

class HomeController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('admin.home', [
            'totalBranch' => count($branches)
        ]);
    }
}
