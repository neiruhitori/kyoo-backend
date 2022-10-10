<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BranchScheduleTemplateDetail;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function getHolidaysByDate(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);

        $holidays = BranchScheduleTemplateDetail::where('branch_id', Auth::user()->branch_id)
            ->whereBetween('date', [
                $request->from,
                $request->to
            ])
            ->get();
        
        return response()->json($holidays);
    }
}
