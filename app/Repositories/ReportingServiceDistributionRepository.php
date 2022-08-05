<?php

namespace App\Repositories;

use App\Interfaces\ReportingServiceDistributionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportingServiceDistributionRepository implements ReportingServiceDistributionRepositoryInterface 
{
    public function getReport(Request $request = null)
    {
        $tableDate = date('Ym');

        $eventDateFilter = [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ];
        
        if ($request->month) {
            $requestDate = $request->year . '-' . $request->month . '-1';

            $eventDateFilter = [
                Carbon::parse($requestDate)->startOfMonth(),
                Carbon::parse($requestDate)->endOfMonth()
            ];

            $tableDate = Carbon::parse($requestDate)->format('Ym');
        }

        if ($request->date) {
            $eventDateFilter = [
                Carbon::parse($request->date)->startOfDay(),
                Carbon::parse($request->date)->endOfDay()
            ];

            $tableDate = Carbon::parse($request->date)->format('Ym');
        }

        $table =  'service_distribution_general_report_' . $tableDate;

        if (!Schema::hasTable($table)) {
            return collect([]);
        }

        $data = DB::table($table)
            ->select(
                'service_id', 'branch_id', 'department_id', 'name',
                DB::raw(
                    'SUM(_0_5) AS _0_5,
                    SUM(_5_10) AS _5_10,
                    SUM(_10_15) AS _10_15,
                    SUM(_15_20) AS _15_20,
                    SUM(_20_25) AS _20_25,
                    SUM(_25_30) AS _25_30,
                    SUM(_30_) AS _30_'
                )
            )
            ->whereBetween('event_date', $eventDateFilter)
            ->where('department_id', $request->department_id)
            ->groupBy('service_id', 'name', 'department_id', 'branch_id')
            ->get();
        
        return $data;
    }
}