<?php

namespace App\Http\Controllers\CS\FeatureMenus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use Auth;
use App\Service;
use DB;
use Illuminate\Support\Carbon;
use App\Department;
use App\Workstation;
use App\User;
use App\Models\CounterActivity;

class MonitoringController extends Controller
{

    public function department()
    {
        return view('cs.featureMenus.monitoring.department', [
            'departments' => Department::where('branch_id', Auth::user()->branch_id)->get()
        ]);
    }

    public function getDataDepartement(Request $request, $id)
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

        $value->now_waiting_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->orderByDesc('created_at')
            ->limit(1)
            ->first()
            ->waiting_duration;

        $value->max_waiting_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->max('waiting_duration');

        $value->avg_waiting_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->avg('waiting_duration');
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

        $value->now_served_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->orderByDesc('created_at')
            ->limit(1)
            ->first()
            ->serving_duration;

        $value->max_served_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->max('serving_duration');

        $value->avg_served_duration = (int) DirectQueue::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->where('service_id', $value->id)
            ->avg('serving_duration');
    }
    

    public function service()
    {
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();
        $services = [];

        if (count($departments) > 0) {
            $services = Service::where('department_id', $departments[0]->id)->get();
        }

        return view('cs.featureMenus.monitoring.service', [
            'departments' => $departments,
            'services' => $services
        ]);
    }

    
    public function getDataService($id)
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
                ->where('workstation_id', $value->id)
                ->whereIn('status', ['served', 'end served'])
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
            
            $activity = CounterActivity::where([
                'date' => date('Y-m-d'),
                'workstation_id' => $value->id
            ])->first();
             
            $value->now_operation_duration = $activity ? $activity->operation_duration : 0;
            $value->now_waiting_duration = 0;
            $value->avg_waiting_duration = 0;
            
            if (
                DirectQueue::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('workstation_id', $value->id)->count()
            ) {
                $nowWaitingDuration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                    ->where('workstation_id', $value->id)
                    ->orderBy('created_at')
                    ->first()
                    ->waiting_duration;
                
                $avgWaitingDuration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                    ->where('workstation_id', $value->id)
                    ->avg('waiting_duration');

                $value->now_waiting_duration = $nowWaitingDuration;
                $value->avg_waiting_duration = floor($avgWaitingDuration);
            }

            $onlineStatus = false;

            if ($value->user->last_login) {
                $expiredAt = Carbon::parse($value->user->last_login)->add(env('SESSION_LIFETIME'), 'minutes');

                $onlineStatus = $expiredAt > Carbon::now();
            }

            $value->is_online = $onlineStatus;
            
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
