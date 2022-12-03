<?php

namespace App\Services;

use App\Branch;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    protected function getDepartmentTable($year, $month)
    {
        return "department_general_report_{$year}{$month}";
    }
}