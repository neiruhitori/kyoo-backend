<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use Auth;
use App\Service;
use DB;
use Illuminate\Support\Carbon;
use App\Department;

class DepartmentMonitoringController extends Controller
{
    public function index()
    {
        return view('adminBranch.monitoring.department', [
            'departments' => Department::where('branch_id', Auth::user()->branch_id)->get()
        ]);
    }

    public function getData(Request $request, $id)
    {
        // Check queue type
        $data = Service::where([
            'branch_id' => Auth::user()->branch_id,
            'department_id' => $id
        ])->get();

        if (Auth::user()->Branch->queue_type === 'exhibition') {
            $data = [];
        }
        
        if (Auth::user()->Branch->queue_type === 'appointment') {
            $data = [];
        }

        if (Auth::user()->Branch->queue_type === 'onsite') {
            $data = $data->map(function ($value) {
                // Total status aggregate
                $this->transformTotalValue($value);
                
                // Waiting duration aggregate
                $this->transformWaitingDuration($value);

                // Served duration aggregate
                $this->transformServedDuration($value);

                return $value;
            });
        }

        // return json
        return response()->json($data, 200);
    }

    private function transformTotalValue(&$value)
    {
        $value->total_waiting = DirectQueue::whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ])
            ->where('service_id', $value->id)
            ->where('status', 'waiting')
            ->count();

        $value->total_served = DirectQueue::whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ])
            ->where('service_id', $value->id)
            ->whereIn('status', ['served', 'end served'])
            ->count();

        $value->total_no_show = DirectQueue::whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ])
            ->where('service_id', $value->id)
            ->where('status', 'no show')
            ->count();
    }

    private function transformWaitingDuration(&$value)
    {
        $total_queues = DirectQueue::whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ])
            ->where('service_id', $value->id)
            ->count();
        
        if ($total_queues < 1) {
            $value->now_waiting_duration = 0;
            $value->max_waiting_duration = 0;
            $value->avg_waiting_duration = 0;
            return;
        }

        $value->now_waiting_duration = (int) DirectQueue::selectRaw(
            DB::raw('EXTRACT(EPOCH FROM (called_at - created_at)) as now_waiting_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->orderByDesc('created_at')
            ->limit(1)
            ->first()
            ->now_waiting_duration;

        $value->max_waiting_duration = (int) DirectQueue::selectRaw(
            DB::raw('MAX(EXTRACT(EPOCH FROM (called_at - created_at))) as max_waiting_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->limit(1)
            ->first()
            ->max_waiting_duration;

        $value->avg_waiting_duration = (int) DirectQueue::selectRaw(
            DB::raw('AVG(EXTRACT(EPOCH FROM (called_at - created_at))) as avg_waiting_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->limit(1)
            ->first()
            ->avg_waiting_duration;
    }

    private function transformServedDuration(&$value)
    {
        $total_queues = DirectQueue::whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ])
            ->where('service_id', $value->id)
            ->count();
        
        if ($total_queues < 1) {
            $value->now_served_duration = 0;
            $value->max_served_duration = 0;
            $value->avg_served_duration = 0;
            return;
        }

        $value->now_served_duration = (int) DirectQueue::selectRaw(
            DB::raw('EXTRACT(EPOCH FROM (done_at - called_at)) as now_served_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->orderByDesc('created_at')
            ->limit(1)
            ->first()
            ->now_served_duration;

        $value->max_served_duration = (int) DirectQueue::selectRaw(
            DB::raw('MAX(EXTRACT(EPOCH FROM (done_at - called_at))) as max_served_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->limit(1)
            ->first()
            ->max_served_duration;

        $value->avg_served_duration = (int) DirectQueue::selectRaw(
            DB::raw('AVG(EXTRACT(EPOCH FROM (done_at - called_at))) as avg_served_duration')
        )
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->limit(1)
            ->first()
            ->avg_served_duration;
    }
}
