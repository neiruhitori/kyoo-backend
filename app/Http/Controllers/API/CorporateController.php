<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CorporateBranchService;

class CorporateController extends Controller
{
    protected CorporateBranchService $corporateBranchService;

    public function __construct(CorporateBranchService $corporateBranchService)
    {
        $this->corporateBranchService = $corporateBranchService;
    }

    public function getCorporateBranches($corporateId)
    {
        try {
            return response()->json($this->corporateBranchService->getCorporateBranches($corporateId));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error'
            ], 500);
        } 
    }
}
