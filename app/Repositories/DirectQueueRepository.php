<?php

namespace App\Repositories;

use App\Interfaces\DirectQueueRepositoryInterface;
use App\DirectQueue;
use App\Service;
use App\Schedule;
use App\ScheduleTemplateDetail;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DirectQueueRepository implements DirectQueueRepositoryInterface 
{
    public function store($data)
    {
        $service = Service::find($data['service_id']);
        $branch = $service->Branch;
        
        // user cant create same direct queue 3x at same date
        $total_same_user_queue = 0;
        if (isset($data['email'])) {
            $total_queue = DirectQueue::where('email', $data['email'])
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            
            if ($total_queue > $total_same_user_queue) {
                $total_same_user_queue = $total_queue;
            }
        }
        if (isset($data['phone'])) {
            $total_queue = DirectQueue::where('phone', $data['phone'])
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            
            if ($total_queue > $total_same_user_queue) {
                $total_same_user_queue = $total_queue;
            }
        }
        if (isset($data['client_id'])) {
            $total_queue = DirectQueue::where('client_id', $data['client_id'])
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            
            if ($total_queue > $total_same_user_queue) {
                $total_same_user_queue = $total_queue;
            }
        }
        if ($total_same_user_queue >= 3) {
            throw new \Exception('Batas antrian maksimal harian untuk pengantri telah terlampaui');
        }
        
        // free license branch cannot create more than 100 queue
        $total_current_booking = DirectQueue::where('service_id', $data['service_id'])
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        if (!$branch->BranchType->is_premium && $total_current_booking >= 100) {
            throw new \Exception('Batas maksimal harian untuk cabang berlisensi gratis telah terlampaui');
        }

        // cant create direct queue on closed day by schedule template
        $holiday = ScheduleTemplateDetail::where('schedule_template_id', $branch->schedule_template_id)
            ->where('date', date('Y-m-d'))
            ->first();
        if ($holiday) {
            throw new \Exception('Cabang sedang tutup hari ini');
        }

        // cant create direct queue on closed day
        $schedule = Schedule::where('branch_id', $service->branch_id)
            ->where('day', strtolower(date('l')))
            ->first();  
        if ($schedule && $schedule->status == 'closed') {
            throw new \Exception('Cabang sedang tutup hari ini');
        }

        // cant create direct queue before open time and after closed time
        if ($schedule && (date('H:i:s') < $schedule->start_time || date('H:i:s') > $schedule->end_time)) {
            throw new \Exception('Cabang sedang tutup hari ini');
        }

        $data['queue_no'] = $this->generate_queue_number($service->branch_id, $data['service_id']);
        $data['booking_code'] = $this->generate_booking_code();

        return DirectQueue::create($data);
    }

    private function generate_queue_number($branch_id, $service_id) {
        $last_onsite_queue = DirectQueue::where('service_id', $service_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->orderBy('queue_no', 'desc')
            ->first();

        $service_order_no = Service::where('branch_id', $branch_id)
            ->where('id', '<=', $service_id)
            ->count();

        if ($last_onsite_queue) {
            return (int) $last_onsite_queue->queue_no + 1;
        }
        
        return $service_order_no . sprintf('%03s', 1);
    }

    private function generate_booking_code() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $input_length = strlen($permitted_chars);
        $random_string = '';
        for($i = 0; $i < 5; $i++) {
            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
    
        return $random_string;
    }

    public function getHourlyQueueByDepartment($departmentId, Request $request)
    {
        $date = [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ];

        if ($request->date) {
            $date = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];
        }

        $sub = DirectQueue::whereHas('Service', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->select(
                'status',
                DB::raw('EXTRACT(HOUR FROM called_at) AS hour'),
                DB::raw('COUNT(*) AS total')
            )
            ->whereIn('status', ['served', 'end served', 'no show'])
            ->whereBetween('created_at', $date)
            ->groupBy('status', 'hour')
            ->orderBy('hour');
        
        return DB::table(DB::raw("({$sub->toSql()}) AS sub"))
            ->mergeBindings($sub->getQuery())
            ->select(
                'hour',
                DB::raw("{$request->department_id} AS department_id"),
                DB::raw('SUM(CASE
                    WHEN status != \'no show\' THEN total
                    ELSE 0
                END) AS total_served'),
                DB::raw('SUM(CASE
                    WHEN status = \'no show\' THEN total
                    ELSE 0
                END) AS total_no_show'),
            )
            ->groupBy('hour')
            ->get();
    }

    public function getHourlyQueueByService($id, Request $request)
    {
        $date = [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ];

        if ($request->date) {
            $date = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];
        }

        $sub = DirectQueue::select(
                'status',
                DB::raw('EXTRACT(HOUR FROM called_at) AS hour'),
                DB::raw('COUNT(*) AS total')
            )
            ->where('service_id', $id)
            ->whereIn('status', ['served', 'end served', 'no show'])
            ->whereBetween('created_at', $date)
            ->groupBy('status', 'hour')
            ->orderBy('hour');
        
        return DB::table(DB::raw("({$sub->toSql()}) AS sub"))
            ->mergeBindings($sub->getQuery())
            ->select(
                'hour',
                DB::raw("{$request->service_id} AS service_id"),
                DB::raw('SUM(CASE
                    WHEN status != \'no show\' THEN total
                    ELSE 0
                END) AS total_served'),
                DB::raw('SUM(CASE
                    WHEN status = \'no show\' THEN total
                    ELSE 0
                END) AS total_no_show'),
            )
            ->groupBy('hour')
            ->get();
    }

    public function getHourlyQueueByWorkstation($id, Request $request)
    {
        $date = [
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        ];

        if ($request->date) {
            $date = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];
        }

        $sub = DirectQueue::select(
                'status',
                DB::raw('EXTRACT(HOUR FROM called_at) AS hour'),
                DB::raw('COUNT(*) AS total')
            )
            ->where('workstation_id', $id)
            ->whereIn('status', ['served', 'end served', 'no show'])
            ->whereBetween('created_at', $date)
            ->groupBy('status', 'hour')
            ->orderBy('hour');
        
        return DB::table(DB::raw("({$sub->toSql()}) AS sub"))
            ->mergeBindings($sub->getQuery())
            ->select(
                'hour',
                DB::raw("{$request->workstation_id} AS workstation_id"),
                DB::raw('SUM(CASE
                    WHEN status != \'no show\' THEN total
                    ELSE 0
                END) AS total_served'),
                DB::raw('SUM(CASE
                    WHEN status = \'no show\' THEN total
                    ELSE 0
                END) AS total_no_show'),
            )
            ->groupBy('hour')
            ->get();
    }
}