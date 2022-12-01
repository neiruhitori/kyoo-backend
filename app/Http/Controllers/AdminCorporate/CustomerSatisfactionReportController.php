<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate;
use App\DirectQueue;
use App\Services\CorporateBranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerSatisfactionReportController extends Controller
{
    protected CorporateBranchService $corporateBranchService;

    public function __construct(CorporateBranchService $corporateBranchService)
    {
        $this->corporateBranchService = $corporateBranchService;
    }

    public function index(Request $request)
    {
        $corporate = Corporate::find(Auth::user()->corporate_id);

        $branches = $this->corporateBranchService->getCorporateBranches(Auth::user()->corporate_id);

        $branchIds = $branches->map(function ($branch) {
            return $branch->id;
        });
        if ($request->branch_id) {
            $branchIds = [$request->branch_id];
        }

        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        return view('adminCorporate.customerSatisfactionReport', [
            'corporate' => $corporate,
            'branches' => $branches,
            'branch_id' => $request->branch_id ?? null,
            'month' => $month,
            'year' => $year,
            'reports' => $this->getReports($branchIds, $year, $month)
        ]);
    }

    protected function getReports($branchIds, $year, $month)
    {
        if (count($branchIds) < 1) {
            return [];
        }

        $reports =  DirectQueue::select(
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS total_queue'),
            DB::raw('COUNT(rating) AS total_feedback'),
            DB::raw('SUM(rating) AS total_rating')
        )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('branch_id', $branchIds)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $reports = $reports->map(function ($report) {
            $report->date = date('j F Y', strtotime($report->date));
            $report->average_rate = $report->total_feedback ? $report->total_rating / $report->total_feedback : 0;
            $report->feedback_percentage = (int) floor($report->total_feedback / $report->total_queue * 100);

            return $report;
        });

        return $reports;
    }
}
