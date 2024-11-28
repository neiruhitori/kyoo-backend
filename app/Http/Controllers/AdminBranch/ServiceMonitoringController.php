<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Department;
use App\Service;
use App\Workstation;
use App\User;
use App\DirectQueue;
use App\Models\CounterActivity;
use Illuminate\Support\Carbon;
use Auth;
use Illuminate\Http\Request;

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
        if($id == "all") {
            $branch_id = Auth::user()->Branch->id;
            $workstations = Workstation::whereHas('WorkstationService.Service.Branch', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->get();
        } else {
            $workstations = Workstation::whereHas('WorkstationService', function ($query) use ($id) {
                $query->where('service_id', $id);
            })->get();
        }
        
        $workstations->map(function ($value) {
            // Ambil user yang terkait dengan workstation
            $value->user = User::whereHas('WorkstationVct', function ($query) use ($value) {
                $query->where('workstation_id', $value->id);
            })->first();
        
            // Hitung total antrian berdasarkan status
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
            
            // Ambil aktivitas untuk workstation
            $activity = CounterActivity::where([
                'date' => date('Y-m-d'),
                'workstation_id' => $value->id
            ])->first();
                 
            $value->now_waiting_duration = 0;
            $value->avg_waiting_duration = 0;
            
            if (DirectQueue::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('workstation_id', $value->id)->count()) {
                $nowWaitingDuration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                ->where('workstation_id', $value->id)
                ->latest()
                ->first()
                ->waiting_duration;
                
                $avgWaitingDuration = DirectQueue::whereBetween('created_at', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                ->where('workstation_id', $value->id)
                ->avg('waiting_duration');
        
                $value->now_waiting_duration = $nowWaitingDuration ?? 0; // Pastikan tidak null
                $value->avg_waiting_duration = floor($avgWaitingDuration);
            }
        
            // Cek status online
            $value->is_online = false; // Default status offline
        
            if ($value->user) { // Pastikan user tidak null
                if ($value->user->last_login) {
                    $expiredAt = Carbon::parse($value->user->last_login)->add(env('SESSION_LIFETIME'), 'minutes');
                    $value->is_online = $expiredAt > Carbon::now();

                    $lastLogin = Carbon::parse($value->user->last_login);
                    $now = Carbon::now();
                    $duration = $now->diff($lastLogin);
                    $lifetime = ($duration->h * 3600) + ($duration->i * 60) + $duration->s;
                }
            }
            $value->now_operation_duration = ($value->user && $value->user->last_login && $activity) 
            ? $lifetime 
            : ($activity ? $activity->operation_duration : 0); // Atau nilai default lainnya
        
            return $value; // Kembalikan value
        });
        
        // Filter workstation yang tidak memiliki pengguna
        $filteredWorkstations = $workstations->filter(function ($value) {
            return $value->user !== null; // Hanya ambil workstation yang memiliki user
        });
        // Urutkan berdasarkan nama petugas dan nama workstation
        $sortedWorkstations = $filteredWorkstations->sortBy(function ($workstation) {
            return [$workstation->name ?? '', $workstation->user->name];
        });

        // Kembalikan hasil
        return response()->json($sortedWorkstations->values()->all());

        
        // return response()->json($filteredWorkstations);
    }

    public function getServiceByDepartment($id)
    {
        $services = Service::where('department_id', $id)->get();

        return response()->json($services);
    }
}
