<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;

class ServiceCategoryController extends Controller
{
    public function getAllByBranchId($branch_id)
    {
        $service_categories = ServiceCategory::where('branch_id', $branch_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'get all service categories by branch id',
            'data' => $service_categories
        ]);
    }
}
