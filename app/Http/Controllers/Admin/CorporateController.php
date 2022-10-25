<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate;

class CorporateController extends Controller
{
    public function index()
    {
        $data = [
            'corporates' => Corporate::active()->get()
        ];

        return view('admin.corporate.index', $data);
    }

    public function create()
    {
        return view('admin.corporate.create');
    }
}
