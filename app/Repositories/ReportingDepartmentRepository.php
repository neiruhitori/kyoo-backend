<?php

namespace App\Repositories;

use App\Interfaces\ReportingDepartmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportingDepartmentRepository implements ReportingDepartmentRepositoryInterface 
{
    public function getReport(Request $request = null)
    {
        $tableDate = date('Y_m_');

        $createdAtFilter = [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ];
        
        if ($request->month) {
            $requestDate = $request->year . '-' . $request->month . '-1';

            $createdAtFilter = [
                Carbon::parse($requestDate)->startOfMonth(),
                Carbon::parse($requestDate)->endOfMonth()
            ];

            $tableDate = Carbon::parse($requestDate)->format('Y_m_');
        }

        if ($request->date) {
            $createdAtFilter = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];

            $tableDate = Carbon::parse($request->date)->format('Y_m_');
        }

        $table = $tableDate . 'department_report';

        if (!Schema::hasTable($table)) {
            return collect([]);
        }

        $data = DB::table($table)
            ->select(
                DB::raw(
                    'department_id,
                    MIN(branch_id) AS branch_id,
                    MIN(name) AS name,
                    SUM(total_queue) AS total_queue,
                    SUM(total_served) AS total_served,
                    SUM(total_no_show) AS total_no_show,
                    SUM(shortest_wait_duration) AS shortest_wait_duration,
                    SUM(average_wait_duration) AS average_wait_duration,
                    SUM(longest_wait_duration) AS longest_wait_duration,
                    SUM(shortest_serve_duration) AS shortest_serve_duration,
                    SUM(average_serve_duration) AS average_serve_duration,
                    SUM(longest_serve_duration) AS longest_serve_duration'
                )
            )
            ->whereBetween('created_at', $createdAtFilter)
            ->where('branch_id', $request->branch_id)
            ->groupBy('department_id')
            ->get();
        
        return $data;
    }
}