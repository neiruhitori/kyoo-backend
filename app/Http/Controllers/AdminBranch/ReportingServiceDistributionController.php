<?php

namespace App\Http\Controllers\AdminBranch;

use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Interfaces\ReportingServiceDistributionRepositoryInterface;
use App\Exports\ReportingServiceDistributionExport;

class ReportingServiceDistributionController extends Controller
{
    private $months;
    private ReportingServiceDistributionRepositoryInterface $reportingServiceDistribution;

    public function __construct(ReportingServiceDistributionRepositoryInterface $reportingServiceDistributionRepo)
    {
        $this->reportingServiceDistribution = $reportingServiceDistributionRepo;
        $this->months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    public function index()
    {
        return view('adminBranch.report.serviceDistribution', [
            'title' => 'Laporan Distribusi Tunggu Layanan',
            'departments' => Department::where('branch_id', Auth::user()->branch_id)->get()
        ]);
    }

    public function getAll(Request $request)
    {
        $data = $this->getData($request);
        
        return response()->json($data);
    }

    private function getData(Request $request)
    {
        $request->branch_id = Auth::user()->branch_id;

        $data = $this->reportingServiceDistribution->getReport($request);
        
        return $data->map(function ($value) {
            $total = 0;

            $total += $value->_0_5;
            $total += $value->_5_10;
            $total += $value->_10_15;
            $total += $value->_15_20;
            $total += $value->_20_25;
            $total += $value->_25_30;
            $total += $value->_30_;

            $value->branch_id = (int) $value->branch_id;
            $value->department_id = (int) $value->department_id;
            $value->service_id = (int) $value->service_id;
            $value->_0_5 = (int) $value->_0_5;
            $value->_0_5_percentage = ($value->_0_5 / $total) * 100;
            $value->_5_10 = (int) $value->_5_10;
            $value->_5_10_percentage = ($value->_5_10 / $total) * 100;
            $value->_10_15 = (int) $value->_10_15;
            $value->_10_15_percentage = ($value->_10_15 / $total) * 100;
            $value->_15_20 = (int) $value->_15_20;
            $value->_15_20_percentage = ($value->_15_20 / $total) * 100;
            $value->_20_25 = (int) $value->_20_25;
            $value->_20_25_percentage = ($value->_20_25 / $total) * 100;
            $value->_25_30 = (int) $value->_25_30;
            $value->_25_30_percentage = ($value->_25_30 / $total) * 100;
            $value->_30_ = (int) $value->_30_;
            $value->_30__percentage = ($value->_30_ / $total) * 100;
            $value->total = $total;

            return $value;
        });
    }

    private function getExportData(Request $request)
    {
        $data = $this->getData($request)
            ->map(function ($value) {
                $value->_0_5_percentage = number_format($value->_0_5_percentage, 2) . '%';
                $value->_5_10_percentage = number_format($value->_5_10_percentage, 2) . '%';
                $value->_10_15_percentage = number_format($value->_10_15_percentage, 2) . '%';
                $value->_15_20_percentage = number_format($value->_15_20_percentage, 2) . '%';
                $value->_20_25_percentage = number_format($value->_20_25_percentage, 2) . '%';
                $value->_25_30_percentage = number_format($value->_25_30_percentage, 2) . '%';
                $value->_30__percentage = number_format($value->_30__percentage, 2) . '%';

                return $value;
            });

        return $data;
    }

    public function exportToPdf(Request $request)
    {
        $date = date('n') . '-' . date('Y');

        if ($request->month) {
            $date = __($this->months[(int) $request->month - 1]) . '-' . $request->year;
            $reportTime = __($this->months[(int) $request->month - 1]) . ' ' . $request->year;
        }

        if ($request->date) {
            $date = $request->date;
            $reportTime = $request->date;
        }

        $data = $this->getExportData($request);

        $pdf = Pdf::loadView('exports.report.serviceDistribution', [
            'title' => __('Waiting Service Distribution Report'),
            'branch' => Auth::user()->Branch,
            'reportTime' => __($reportTime),
            'department' => Department::find($request->department_id),
            'data' => $data
        ])
            ->setPaper('a4', 'potrait');

        return $pdf->download(__('Waiting Service Distribution Report').'_' . $date . '.pdf');
    }

    public function exportToExcel(Request $request)
    {
        $request->branch_id = Auth::user()->branch_id;

        $date = date('n') . '-' . date('Y');

        if ($request->month) {
            $date = __($this->months[(int) $request->month - 1]) . '-' . $request->year;
        }

        if ($request->date) {
            $date = $request->date;
        }

        return Excel::download(
            new ReportingServiceDistributionExport($request, $this->reportingServiceDistribution),
            __('Waiting Service Distribution Report').'_' . $date . '.xlsx'
        );
    }
}
