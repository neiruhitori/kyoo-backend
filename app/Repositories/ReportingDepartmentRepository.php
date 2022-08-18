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
        $tableDate = date('Ym');

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

            $tableDate = Carbon::parse($requestDate)->format('Ym');
        }

        if ($request->date) {
            $createdAtFilter = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];

            $tableDate = Carbon::parse($request->date)->format('Ym');
        }

        $table = 'department_general_report_' . $tableDate;

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
            ->whereBetween('event_date', $createdAtFilter)
            ->where('branch_id', $request->branch_id)
            ->groupBy('department_id')
            ->get();
        
        return $data;
    }

    public function getDailyQueueByDepartment($id, Request $request)
    {
        if ((int) $request->month < 10) {
            $request->month = '0' . $request->month;
        }

        $table = 'department_general_report_' . $request->year . $request->month;

        $dateBetween = [
            Carbon::parse("{$request->year}-{$request->month}-01")->startOfMonth(),
            Carbon::parse("{$request->year}-{$request->month}-01")->endOfMonth()
        ];

        return DB::table($table)->select(
            DB::raw("{$request->department_id} AS department_id"),
            DB::raw('EXTRACT(DOW FROM event_date) AS day'),
            DB::raw('SUM(total_served) AS total_served'),
            DB::raw('SUM(total_no_show) AS total_no_show'),
        )
            ->where('department_id', $id)
            ->whereBetween('event_date', $dateBetween)
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    public function getMonthlyQueueByDepartment($id, Request $request)
    {
        $responses = [];
        $tables = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $i + 1;

            if ($month < 10) {
                $month = '0' . $month;
            }

            $table = 'department_general_report_' . $request->year . $month;

            if (Schema::hasTable($table)) {
                array_push($tables, (object) [
                    'month' => $i,
                    'year' => $request->year,
                    'table' => $table
                ]);
            }
        }

        for ($i = 0; $i < count($tables); $i++) {
            $row = DB::table($tables[$i]->table)
                ->select(
                    'department_id',
                    DB::raw("{$tables[$i]->month} AS month"),
                    DB::raw('SUM(total_served) AS total_served'),
                    DB::raw('SUM(total_no_show) AS total_no_show')
                )
                ->where('department_id', $id)
                ->groupBy('department_id')
                ->first();
            
            if ($row) {
                array_push($responses, $row);
            }
        }

        return collect($responses);
    }
}