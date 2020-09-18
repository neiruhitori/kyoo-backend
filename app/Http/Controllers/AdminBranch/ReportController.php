<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use Auth;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        // only can see report within last two months
        $date = $request->date ?: date('Y-m-d');
        $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
        if($request->date && date('Y-m-d', strtotime($request->date)) < $last_month){
            $request->session()->flash('error', 'Can not select report more then last 2 months!');
            return view('adminBranch.report.daily', [
            'appointments' => [],
            'date' => $date,
            'service_id' => $request->service_id
        ]);
        }

        $appointments = Appointment::whereHas('Slot.Service', function($query) use ($request){
            $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $date)->orderBy('number')->get();
        return view('adminBranch.report.daily', [
            'appointments' => $appointments,
            'date' => $date,
            'service_id' => $request->service_id
        ]);
    }
}
