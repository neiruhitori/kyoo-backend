<?php

namespace App\Services;

use App\Branch;
use App\DirectQueue;
use App\Service;
use Illuminate\Support\Carbon;

class MonitoringService {
    public function monitorBranches($corporateId)
    {
        if (!$corporateId) {
            throw new \Exception('Corporate ID harus diisi');
        }

        $branches = Branch::onsite()->where('corporate_id', $corporateId)->get();
        if (count($branches) < 1) {
            return [];
        }

        $branchIds = $branches->map(function ($value) {
            return $value->id;
        })->toArray();

        $queues = DirectQueue::whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->get()
            ->groupBy('branch_id');

        return $branches->map(function ($branch) use ($queues) {
            $value = (object) [
                'id' => $branch->id,
                'name' => $branch->name,
                'is_today_open' => $branch->is_today_open,
                'totalWaiting' => 0,
                'totalServed' => 0,
                'totalNoShow' => 0,
                'nowWaitingDuration' => 0,
                'avgWaitingDuration' => 0,
                'maxWaitingDuration' => 0,
                'nowServingDuration' => 0,
                'avgServingDuration' => 0,
                'maxServingDuration' => 0
            ];

            if (!isset($queues[$branch->id]) || count($queues[$branch->id]) < 1) {
                return $value;
            }
            
            $value->totalWaiting = $queues[$branch->id]->filter->isWaiting()->count();
            $value->totalServed = $queues[$branch->id]->filter->isServed()->count();
            $value->totalNoShow = $queues[$branch->id]->filter->isNoShow()->count();
            $value->nowWaitingDuration = $queues[$branch->id]->last()->waiting_duration;
            $value->maxWaitingDuration = $queues[$branch->id]->max('waiting_duration');
            $value->avgWaitingDuration = $queues[$branch->id]->avg('waiting_duration');
            $value->nowServingDuration = $queues[$branch->id]->last()->serving_duration;
            $value->maxServingDuration = $queues[$branch->id]->max('serving_duration');
            $value->avgServingDuration = $queues[$branch->id]->avg('serving_duration');

            return $value;
        });
    }

    public function monitorServices($branchId)
    {
        if (!$branchId) {
            throw new \Exception('Branch ID harus diisi');
        }

        $services = Service::where('branch_id', $branchId)->get();
        if (count($services) < 1) {
            return [];
        }

        $serviceIds = $services->map(function ($value) {
            return $value->id;
        })->toArray();

        $queues = DirectQueue::whereIn('service_id', $serviceIds)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->get()
            ->groupBy('service_id');
        
        return $services->map(function ($service) use ($queues) {
            $value = (object) [
                'id' => $service->id,
                'name' => $service->name,
                'department' => (object) [
                    'id' => $service->Department->id,
                    'name' => $service->Department->name
                ],
                'totalWaiting' => 0,
                'totalServed' => 0,
                'totalNoShow' => 0,
                'nowWaitingDuration' => 0,
                'avgWaitingDuration' => 0,
                'maxWaitingDuration' => 0,
                'nowServingDuration' => 0,
                'avgServingDuration' => 0,
                'maxServingDuration' => 0
            ];

            if (!isset($queues[$service->id]) || count($queues[$service->id]) < 1) {
                return $value;
            }
            
            $value->totalWaiting = $queues[$service->id]->filter->isWaiting()->count();
            $value->totalServed = $queues[$service->id]->filter->isServed()->count();
            $value->totalNoShow = $queues[$service->id]->filter->isNoShow()->count();
            $value->nowWaitingDuration = $queues[$service->id]->last()->waiting_duration;
            $value->maxWaitingDuration = $queues[$service->id]->max('waiting_duration');
            $value->avgWaitingDuration = $queues[$service->id]->avg('waiting_duration');
            $value->nowServingDuration = $queues[$service->id]->last()->serving_duration;
            $value->maxServingDuration = $queues[$service->id]->max('serving_duration');
            $value->avgServingDuration = $queues[$service->id]->avg('serving_duration');

            return $value;
        });
    }
}