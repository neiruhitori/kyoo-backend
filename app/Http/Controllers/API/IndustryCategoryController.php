<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\IndustryCategory;

class IndustryCategoryController extends Controller
{
    public function index()
    {
        $categories = IndustryCategory::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'message' => 'get all industry category',
            'data' => $categories
        ]);
    }
}
