<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BranchScheduleTemplateDetail;

class HolidayController extends Controller
{
    public function getHolidayByBranchId($id)
    {
        $holidays = BranchScheduleTemplateDetail::where('branch_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'get holidays by branch id',
            'data' => $holidays
        ]);
    }
}
