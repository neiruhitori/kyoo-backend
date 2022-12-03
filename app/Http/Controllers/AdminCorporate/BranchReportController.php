<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Supports\DateFormat;

use App\Services\ReportingService;
use App\Services\CorporateBranchService;

class BranchReportController extends Controller
{
    protected ReportingService $reportingService;
    protected CorporateBranchService $corporateBranchService;

    public function __construct(ReportingService $reportingService, CorporateBranchService $corporateBranchService)
    {
        $this->reportingService = $reportingService;
        $this->corporateBranchService =  $corporateBranchService;
    }

    public function index(Request $request)
    {
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        $branches = $this->corporateBranchService->getCorporateBranches(Auth::user()->corporate_id);

        $params =  ['year' => $year, 'month' => $month];
        if ($request->branch_id) {
            $params['branch_id'] = $request->branch_id;
        }
        $reports = $this->reportingService->findBranchReports(
            Auth::user()->corporate_id,
            $params
        );

        $reports = $reports->map(function ($r) {
            $r->average_wait_duration = DateFormat::secondsToTime($r->average_wait_duration);
            $r->longest_wait_duration = DateFormat::secondsToTime($r->longest_wait_duration);
            $r->average_serve_duration = DateFormat::secondsToTime($r->average_serve_duration);
            $r->longest_serve_duration = DateFormat::secondsToTime($r->longest_serve_duration);

            return $r;
        });

        return view('adminCorporate.branchReport', [
            'branches' => $branches,
            'branch_id' => $request->branch_id,
            'month' => $month,
            'year' => $year,
            'reports' => $reports
        ]);
    }
}
