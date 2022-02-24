<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\DirectQueue;
use App\WorkstationService;
use Auth;
class ReportController extends Controller
{
    public function daily(Request $request)
    {
        // only can see report within last two months
        $date = $request->date ?: date('Y-m-d');
        $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
        if($request->date && date('Y-m-d', strtotime($request->date)) < $last_month){
            $request->session()->flash('error', __('Can not select report more then last 2 months'));
            return view('cs.report.daily', [
            'appointments' => [],
            'date' => $date,
            'service_id' => $request->service_id,
            'success' => false
        ]);
        }

        $appointments = Appointment::whereHas('Slot.Service', function($query) use ($request){
            $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $date)->orderBy('number')->get();

        return view('cs.report.daily', [
            'appointments' => $appointments,
            'date' => $date,
            'service_id' => $request->service_id,
            'success' => true
        ]);
    }

    public function directQueueDaily(Request $request)
    {
        // only can see report within last two months
        $date = $request->date ?: date('Y-m-d');
        if (Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
            if($request->date && date('Y-m-d', strtotime($request->date)) < $last_month){
                $request->session()->flash('error', __('Can not select report more then last 2 months'));
                return view('cs.report.directQueue.daily', [
                    'appointments' => [],
                    'date' => $date,
                    'service_id' => $request->service_id,
                    'success' => false
                ]);
            }
        }

        $directQueue = DirectQueue::query()->whereHas('WorkstationService.Service', function($query){
            $query->whereBranchId(Auth::user()->branch_id);
        })->whereDate('created_at', $date)->orderBy('created_at');


        $directQueue->when($request->workstation_service_id, function($query) use ($request) {
            $query->whereWorkstationServiceId($request->workstation_service_id);
        });

        $workstationServices = WorkstationService::whereHas('Workstation.Department', function($query){
            $query->whereBranchId(Auth::user()->branch_id);
        })->get();

        return view('cs.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'date' => $date,
            'workstation_service_id' => $request->workstation_service_id,
            'workstationServices' => $workstationServices,
            'success' => true
        ]);
    }
}
