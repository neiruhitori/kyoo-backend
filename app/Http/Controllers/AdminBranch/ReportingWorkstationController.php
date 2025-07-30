<?php

namespace App\Http\Controllers\AdminBranch;

use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Interfaces\ReportingWorkstationRepositoryInterface;
use App\Exports\ReportingWorkstationExport;
use App\Service;

class ReportingWorkstationController extends Controller
{
    private $months;
    private ReportingWorkstationRepositoryInterface $reportingWorkstation;

    public function __construct(ReportingWorkstationRepositoryInterface $reportingWorkstationRepo)
    {
        $this->reportingWorkstation = $reportingWorkstationRepo;
        $this->months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    public function index()
    {
        return view('adminBranch.report.workstation', [
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

        $data = $this->reportingWorkstation->getReport($request);
        
        return $data->map(function ($value) {
            $id = $value->workstation_id;
            $services = Service::whereHas('WorkstationService', function ($query) use ($id) {
                $query->where('workstation_id', $id);
            })->get();
            $totalServeDuration = (int) $value->total_serve_duration;
            $totalOperatingDuration = (int) $value->total_operating_duration;
            // $productivityPercentage = ($totalServeDuration / $totalOperatingDuration) * 100;
             $productivityPercentage = $totalOperatingDuration > 0
                                    ? ($totalServeDuration / $totalOperatingDuration) * 100
                                    : 0;
                                    
            return [
                'branch_id' => (int) $value->branch_id,
                'department_id' => (int) $value->department_id,
                'workstation_id' => (int) $value->workstation_id,
                'services' => $services,
                'name' => $value->name,
                'total_queue' => (int) $value->total_queue,
                'total_served' => (int) $value->total_served,
                'total_no_show' => (int) $value->total_no_show,
                'shortest_wait_duration' => (int) $value->shortest_wait_duration,
                'average_wait_duration' => (int) $value->average_wait_duration,
                'longest_wait_duration' => (int) $value->longest_wait_duration,
                'shortest_serve_duration' => (int) $value->shortest_serve_duration,
                'average_serve_duration' => (int) $value->average_serve_duration,
                'longest_serve_duration' => (int) $value->longest_serve_duration,
                'total_operating_duration' => $totalOperatingDuration,
                'total_serve_duration' => $totalServeDuration,
                'total_idle_duration' => $totalOperatingDuration - $totalServeDuration,
                'productivity_percentage' => $productivityPercentage
            ];
        });
    }

    private function getExportData(Request $request)
    {
        $data = $this->getData($request)
            ->map(function ($queue) {
                $queue['services'] = $queue['services']->map(function ($workstation) {
                        return $workstation->name;
                    })
                    ->join(', ');
                $queue['shortest_wait_duration'] = $this->formatTime($queue['shortest_wait_duration']);
                $queue['average_wait_duration'] = $this->formatTime($queue['average_wait_duration']);
                $queue['longest_wait_duration'] = $this->formatTime($queue['longest_wait_duration']);
                $queue['shortest_serve_duration'] = $this->formatTime($queue['shortest_serve_duration']);
                $queue['average_serve_duration'] = $this->formatTime($queue['average_serve_duration']);
                $queue['longest_serve_duration'] = $this->formatTime($queue['longest_serve_duration']);
                $queue['total_serve_duration'] = $this->formatTime($queue['total_serve_duration']);
                $queue['total_operating_duration'] = $this->formatTime($queue['total_operating_duration']);
                $queue['total_idle_duration'] = $this->formatTime($queue['total_idle_duration']);
                $queue['productivity_percentage'] = number_format($queue['productivity_percentage'], 2) . '%';

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

        $pdf = Pdf::loadView('exports.report.workstation', [
            'title' => __('Workstation Report'),
            'branch' => Auth::user()->Branch,
            'reportTime' => __($reportTime),
            'department' => Department::find($request->department_id),
            'data' => $data
        ])
            ->setPaper('a4', 'potrait');

        return $pdf->download( __('Workstation Report').'_' . $date . '.pdf');
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
            new ReportingWorkstationExport($request, $this->reportingWorkstation),
            __('Workstation Report').'_' . $date . '.xlsx'
        );
    }
}
