<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;

class BranchController extends Controller
{
    public function getAllByCityId($regency_id)
    {
        $branches = Branch::with('IndustryCategory')->whereRegencyId($regency_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by city id',
            'data' => $branches
        ]);
    }

    public function getAllByKeyword($keyword)
    {
        $branches = Branch::with('IndustryCategory')->where('name', 'like', "%$keyword%")->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by keyword',
            'data' => $branches
        ]);
    }

    public function getAllByIndustryCategory($industry_category_id)
    {
        $branches = Branch::whereIndustryCategoryId($industry_category_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by industry category id',
            'data' => $branches
        ]);
    }

    public function show(Branch $branch)
    {
        $branch->Schedule;
        $branch->IndustryCategory;
        return response()->json([
            'success' => true,
            'message' => 'get detail branch with schedule and service',
            'data' => $branch
        ]);
    }
}
