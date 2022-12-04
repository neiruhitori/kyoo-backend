<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Supports\DateFormat;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use App\Branch;
use App\Models\Corporate;
use App\Services\CorporateBranchService;
use App\Services\ReportingService;

use App\Exports\ServiceCorporateExport;

class ServiceReportController extends Controller
{
    protected CorporateBranchService $corporateBranchSevice;

    public function __construct(
        CorporateBranchService $corporateBranchService,
        ReportingService $reportingService
    )
    {
        $this->corporateBranchService = $corporateBranchService;
        $this->reportingService = $reportingService;
    }

    public function index(Request $request)
    {
        $branches = $this->corporateBranchService->getCorporateBranches(Auth::user()->corporate_id);

        return view('adminCorporate.serviceReport', [
            'branches' => $branches,
        ]);
    }

    public function getReports(Request $request)
    {
        $reports = collect([]);

        if ($request->date) {
            $reports = $this->reportingService->findServiceDailyReports($request->branch_id, $request->date);
        } else {
            $reports = $this->reportingService->findServiceMonthlyReports($request->branch_id, $request->year, $request->month);
        }

        return $reports->map(function ($r) {
            $r->shortest_wait_duration = DateFormat::secondsToTime($r->shortest_wait_duration);
            $r->average_wait_duration = DateFormat::secondsToTime($r->average_wait_duration);
            $r->longest_wait_duration = DateFormat::secondsToTime($r->longest_wait_duration);
            $r->shortest_serve_duration = DateFormat::secondsToTime($r->shortest_serve_duration);
            $r->average_serve_duration = DateFormat::secondsToTime($r->average_serve_duration);
            $r->longest_serve_duration = DateFormat::secondsToTime($r->longest_serve_duration);

            return $r;
        });
    }

    public function exportToPdf(Request $request)
    {
        $date = null;
        if ($request->month) {
            $date = date('F Y', strtotime("{$request->year}-{$request->month}-1"));
        }
        if ($request->date) {
            $date = $request->date;
        }

        $corporate = Corporate::find(Auth::user()->corporate_id);
        $branch = Branch::find($request->branch_id);
        $data = $this->getReports($request);

        $pdf = Pdf::loadView('adminCorporate.serviceReportPdf', [
            'title' => 'Laporan Layanan',
            'date' => $date,
            'corporate' => $corporate,
            'branch' => $branch,
            'data' => $data
        ])
            ->setPaper('a4', 'potrait');

        return $pdf->download("Laporan_Layanan_{$date}.pdf");
    }

    public function exportToExcel(Request $request)
    {
        $corporate = Corporate::find(Auth::user()->corporate_id);
        $branch = Branch::find($request->branch_id);

        $date = null;
        if ($request->month) {
            $date = date('F Y', strtotime("{$request->year}-{$request->month}-1"));
        }
        if ($request->date) {
            $date = $request->date;
        }

        $params = [
            'corporate' => $corporate,
            'branch' => $branch,
            'date' => $date,
            'data' => $this->getReports($request)
        ];

        return Excel::download(
            new ServiceCorporateExport($params),
            'Laporan_Layanan_' . $date . '.xlsx'
        );
    }
}
