<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Department;
use App\Service;
use App\Workstation;
use App\User;
use App\DirectQueue;
use Illuminate\Support\Carbon;
use Auth;

class ServiceMonitoringController extends Controller
{
    public function index()
    {
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();
        $services = [];

        if (count($departments) > 0) {
            $services = Service::where('department_id', $departments[0]->id)->get();
        }

        return view('adminBranch.monitoring.service', [
            'departments' => $departments,
            'services' => $services
        ]);
    }

    public function show($id)
    {
        $workstations = Workstation::whereHas('WorkstationService', function ($query) use ($id) {
            $query->where('service_id', $id);
        })->get();

        $workstations->map(function ($value) {
            $value->user = User::whereHas('WorkstationVct', function ($query) use ($value) {
                $query->where('workstation_id', $value->id);
            })->first();

            $value->total_waiting = DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
                ->where([
                    'workstation_id' => $value->id,
                    'status' => 'waiting'
                ])
                ->count();
            $value->total_served = DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
                ->where([
                    'workstation_id' => $value->id,
                    'status' => 'waiting'
                ])
                ->count();
            $value->total_no_show = DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
                ->where([
                    'workstation_id' => $value->id,
                    'status' => 'no show'
                ])
                ->count();
            
            $value->now_waiting_duration = 0;
            $value->avg_waiting_duration = 0;
            
            if (DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
                ->where('workstation_id', $value->id)->count()) {
                $value->now_waiting_duration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                    ->where('workstation_id', $value->id)
                    ->orderBy('created_at')
                    ->first()
                    ->waiting_duration;
                $value->avg_waiting_duration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                    ->where('workstation_id', $value->id)
                    ->avg('waiting_duration');
            }
            
            return $value;
        });

        return response()->json($workstations);
    }

    public function getServiceByDepartment($id)
    {
        $services = Service::where('department_id', $id)->get();

        return response()->json($services);
    }
}
