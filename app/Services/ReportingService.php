<?php

namespace App\Services;

use App\Branch;
use App\Service;
use App\Workstation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class ReportingService {
    public function findBranchReports($corporateId, $params)
    {
        if (!Schema::hasTable(
            $this->getDepartmentTable($params['year'], $params['month'])
        )) {
            return collect([]);
        }

        $branchParams = ['corporate_id' => $corporateId];
        if (isset($params['branch_id'])) {
            $branchParams['id'] = $params['branch_id'];
        }
        $branches = Branch::where($branchParams)->get();

        $branchMap = $branches->groupBy('id');
        $branchIds = $branches->map(fn($branch) => $branch->id);

        $reports = DB::table($this->getDepartmentTable($params['year'], $params['month']))
            ->select(
                'branch_id',
                DB::raw('SUM(total_queue) AS total_queue'),
                DB::raw('SUM(total_served) AS total_served'),
                DB::raw('SUM(total_no_show) AS total_no_show'),
                DB::raw('AVG(average_wait_duration) AS average_wait_duration'),
                DB::raw('MAX(longest_wait_duration) AS longest_wait_duration'),
                DB::raw('AVG(average_serve_duration) AS average_serve_duration'),
                DB::raw('MAX(longest_serve_duration) AS longest_serve_duration'),
            )
            ->whereIn('branch_id', $branchIds)
            ->groupBy('branch_id')
            ->get();
        
        $reports = $reports->map(function ($r) use ($branchMap) {
            $r->branch = $branchMap[$r->branch_id]->first();
            return $r;
        });
    
        return $reports;
    }

    public function findServiceDailyReports($branchId, $date)
    {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        if (!Schema::hasTable($this->getServiceTable($year, $month))) {
            return collect([]);
        }

        $serviceMap = Service::where('branch_id', $branchId)->get()->groupBy('id');

        $reports = DB::table($this->getServiceTable($year, $month))
            ->select(
                'service_id',
                DB::raw('SUM(total_queue) AS total_queue'),
                DB::raw('SUM(total_served) AS total_served'),
                DB::raw('SUM(total_no_show) AS total_no_show'),
                DB::raw('AVG(shortest_wait_duration) AS shortest_wait_duration'),
                DB::raw('AVG(average_wait_duration) AS average_wait_duration'),
                DB::raw('MAX(longest_wait_duration) AS longest_wait_duration'),
                DB::raw('AVG(shortest_serve_duration) AS shortest_serve_duration'),
                DB::raw('AVG(average_serve_duration) AS average_serve_duration'),
                DB::raw('MAX(longest_serve_duration) AS longest_serve_duration'),
            )
            ->where('branch_id', $branchId)
            ->whereBetween('event_date', [
                Carbon::parse($date)->startOfDay(),
                Carbon::parse($date)->endOfDay()
            ])
            ->groupBy('service_id')
            ->get();
        
        $reports = $reports->map(function ($r) use ($serviceMap) {
            $r->service = $serviceMap[$r->service_id]->first();
            return $r;
        });

        return $reports;
    }

    public function findServiceMonthlyReports($branchId, $year, $month)
    {
        if (!Schema::hasTable($this->getServiceTable($year, $month))) {
            return collect([]);
        }

        $date = "{$year}-{$month}-1";

        $serviceMap = Service::where('branch_id', $branchId)->get()->groupBy('id');

        $reports = DB::table($this->getServiceTable($year, $month))
            ->select(
                'service_id',
                DB::raw('SUM(total_queue) AS total_queue'),
                DB::raw('SUM(total_served) AS total_served'),
                DB::raw('SUM(total_no_show) AS total_no_show'),
                DB::raw('AVG(shortest_wait_duration) AS shortest_wait_duration'),
                DB::raw('AVG(average_wait_duration) AS average_wait_duration'),
                DB::raw('MAX(longest_wait_duration) AS longest_wait_duration'),
                DB::raw('AVG(shortest_serve_duration) AS shortest_serve_duration'),
                DB::raw('AVG(average_serve_duration) AS average_serve_duration'),
                DB::raw('MAX(longest_serve_duration) AS longest_serve_duration'),
            )
            ->where('branch_id', $branchId)
            ->whereBetween('event_date', [
                Carbon::parse($date)->startOfMonth(),
                Carbon::parse($date)->endOfMonth()
            ])
            ->groupBy('service_id')
            ->get();
        
        $reports = $reports->map(function ($r) use ($serviceMap) {
            $r->service = $serviceMap[$r->service_id]->first();
            return $r;
        });

        return $reports;
    }

    public function findWorkstationDailyReports($branchId, $date)
    {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        if (!Schema::hasTable($this->getWorkstationTable($year, $month))) {
            return collect([]);
        }

        $workstationMap = Workstation::with('WorkstationService.Service')
            ->whereHas('Department', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get()
            ->groupBy('id');

        $reports = DB::table($this->getWorkstationTable($year, $month))
            ->select(
                'workstation_id',
                DB::raw('SUM(total_queue) AS total_queue'),
                DB::raw('SUM(total_served) AS total_served'),
                DB::raw('SUM(total_no_show) AS total_no_show'),
                DB::raw('AVG(shortest_wait_duration) AS shortest_wait_duration'),
                DB::raw('AVG(average_wait_duration) AS average_wait_duration'),
                DB::raw('MAX(longest_wait_duration) AS longest_wait_duration'),
                DB::raw('AVG(shortest_serve_duration) AS shortest_serve_duration'),
                DB::raw('AVG(average_serve_duration) AS average_serve_duration'),
                DB::raw('MAX(longest_serve_duration) AS longest_serve_duration'),
            )
            ->where('branch_id', $branchId)
            ->whereBetween('event_date', [
                Carbon::parse($date)->startOfDay(),
                Carbon::parse($date)->endOfDay()
            ])
            ->groupBy('workstation_id')
            ->get();
        
        $reports = $reports->map(function ($r) use ($workstationMap) {
            $r->workstation = $workstationMap[$r->workstation_id]->first();
            $r->services = $workstationMap[$r->workstation_id]->first()->WorkstationService->map(function ($ws) {
                return $ws->Service;
            });
            return $r;
        });

        return $reports;
    }

    public function findWorkstationMonthlyReports($branchId, $year, $month)
    {
        if (!Schema::hasTable($this->getWorkstationTable($year, $month))) {
            return collect([]);
        }

        $date = "{$year}-{$month}-1";

        $workstationMap = Workstation::with('WorkstationService.Service')
            ->whereHas('Department', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get()
            ->groupBy('id');

        $reports = DB::table($this->getWorkstationTable($year, $month))
            ->select(
                'workstation_id',
                DB::raw('SUM(total_queue) AS total_queue'),
                DB::raw('SUM(total_served) AS total_served'),
                DB::raw('SUM(total_no_show) AS total_no_show'),
                DB::raw('AVG(shortest_wait_duration) AS shortest_wait_duration'),
                DB::raw('AVG(average_wait_duration) AS average_wait_duration'),
                DB::raw('MAX(longest_wait_duration) AS longest_wait_duration'),
                DB::raw('AVG(shortest_serve_duration) AS shortest_serve_duration'),
                DB::raw('AVG(average_serve_duration) AS average_serve_duration'),
                DB::raw('MAX(longest_serve_duration) AS longest_serve_duration'),
            )
            ->where('branch_id', $branchId)
            ->whereBetween('event_date', [
                Carbon::parse($date)->startOfMonth(),
                Carbon::parse($date)->endOfMonth()
            ])
            ->groupBy('workstation_id')
            ->get();
        
        $reports = $reports->map(function ($r) use ($workstationMap) {
            $r->workstation = $workstationMap[$r->workstation_id]->first();
            $r->services = $workstationMap[$r->workstation_id]->first()->WorkstationService->map(function ($ws) {
                return $ws->Service;
            });
            return $r;
        });

        return $reports;
    }

    protected function getDepartmentTable($year, $month)
    {
        return "department_general_report_{$year}{$month}";
    }

    protected function getServiceTable($year, $month)
    {
        return "service_general_report_{$year}{$month}";
    }

    protected function getWorkstationTable($year, $month)
    {
        return "workstation_general_report_{$year}{$month}";
    }
}