<?php

namespace App\Exports;

use App\Interfaces\ReportingDepartmentRepositoryInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Workstation;
use Illuminate\Support\Facades\Auth;

class ReportingDepartmentExport implements FromView
{
    private ReportingDepartmentRepositoryInterface $reportingDepartment;

    public function __construct(Request $request, ReportingDepartmentRepositoryInterface $reportingDepartmentRepo)
    {
        $this->reportingDepartment = $reportingDepartmentRepo;
        $this->request = $request;
    }

    public function view(): View
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data = $this->reportingDepartment->getReport($this->request);

        $date = date('n') . ' ' . date('Y');

        if ($this->request->month) {
            $date = $months[(int) $this->request->month - 1] . ' ' . $this->request->year;
        }

        if ($this->request->date) {
            $date = $this->request->date;
        }

        $data = $data->map(function ($queue) {
            $queue->workstations = Workstation::where('department_id', $queue->department_id)
                ->get()
                ->map(function ($workstation) {
                    return $workstation->name;
                })
                ->join(', ');
            $queue->shortest_wait_duration = $this->formatTime($queue->shortest_wait_duration);
            $queue->average_wait_duration = $this->formatTime($queue->average_wait_duration);
            $queue->longest_wait_duration = $this->formatTime($queue->longest_wait_duration);
            $queue->shortest_serve_duration = $this->formatTime($queue->shortest_serve_duration);
            $queue->average_serve_duration = $this->formatTime($queue->average_serve_duration);
            $queue->longest_serve_duration = $this->formatTime($queue->longest_serve_duration);

            return $queue;
        });

        return view('adminBranch.excel.department', [
            'branch' => Auth::user()->Branch,
            'date' => $date,
            'data' => $data
        ]);
    }

    private function formatTime($value) {
        $hours = floor($value / 3600);
        $minutes = floor(($value % 3600) / 60);
        $seconds = floor($value % 3600 % 60);
        
        if ($hours < 10) $hours = '0' . $hours;
        if ($minutes < 10) $minutes = '0' . $minutes;
        if ($seconds < 10) $seconds = '0' . $seconds;

        return $hours . ':' . $minutes . ':' . $seconds;
    }
}
