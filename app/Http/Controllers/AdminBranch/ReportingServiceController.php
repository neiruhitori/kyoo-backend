<?php

namespace App\Http\Controllers\AdminBranch;

use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Interfaces\ReportingServiceRepositoryInterface;
use App\Exports\ReportingServiceExport;

class ReportingServiceController extends Controller
{
    private $months;
    private ReportingServiceRepositoryInterface $reportingService;

    public function __construct(ReportingServiceRepositoryInterface $reportingServiceRepo)
    {
        $this->reportingService = $reportingServiceRepo;
        $this->months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    public function index()
    {
        return view('adminBranch.report.service', [
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

        $data = $this->reportingService->getReport($request);
        
        return $data->map(function ($value) {
            return [
                'branch_id' => (int) $value->branch_id,
                'department_id' => (int) $value->department_id,
                'service_id' => (int) $value->service_id,
                'name' => $value->name,
                'total_queue' => (int) $value->total_queue,
                'total_served' => (int) $value->total_served,
                'total_no_show' => (int) $value->total_no_show,
                'shortest_wait_duration' => (int) $value->shortest_wait_duration,
                'average_wait_duration' => (int) $value->average_wait_duration,
                'longest_wait_duration' => (int) $value->longest_wait_duration,
                'shortest_serve_duration' => (int) $value->shortest_serve_duration,
                'average_serve_duration' => (int) $value->average_serve_duration,
                'longest_serve_duration' => (int) $value->longest_serve_duration
            ];
        });
    }

    private function getExportData(Request $request)
    {
        $data = $this->getData($request)
            ->map(function ($queue) {
                $queue['shortest_wait_duration'] = $this->formatTime($queue['shortest_wait_duration']);
                $queue['average_wait_duration'] = $this->formatTime($queue['average_wait_duration']);
                $queue['longest_wait_duration'] = $this->formatTime($queue['longest_wait_duration']);
                $queue['shortest_serve_duration'] = $this->formatTime($queue['shortest_serve_duration']);
                $queue['average_serve_duration'] = $this->formatTime($queue['average_serve_duration']);
                $queue['longest_serve_duration'] = $this->formatTime($queue['longest_serve_duration']);

                return $queue;
            });

        return $data;
    }

    private function formatTime($value)
    {
        $hours = floor($value / 3600);
        $minutes = floor(($value % 3600) / 60);
        $seconds = floor($value % 3600 % 60);
        
        if ($hours < 10) $hours = '0' . $hours;
        if ($minutes < 10) $minutes = '0' . $minutes;
        if ($seconds < 10) $seconds = '0' . $seconds;

        return $hours . ':' . $minutes . ':' . $seconds;
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

        $pdf = Pdf::loadView('exports.report.service', [
            'title' => __('Service Report'),
            'branch' => Auth::user()->Branch,
            'reportTime' => __($reportTime),
            'department' => Department::find($request->department_id),
            'data' => $data
        ])
            ->setPaper('a4', 'potrait');

        return $pdf->download( __('Service Report') .'_' . $date . '.pdf');
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
            new ReportingServiceExport($request, $this->reportingService),
            __('Service Report') . '_' . $date . '.xlsx'
        );
    }
}
