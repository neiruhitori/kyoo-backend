<?php

namespace App\Exports;

use App\Interfaces\ReportingServiceRepositoryInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Department;

class ReportingServiceExport implements FromView
{
    private ReportingServiceRepositoryInterface $reportingService;

    public function __construct(Request $request, ReportingServiceRepositoryInterface $reportingServiceRepo)
    {
        $this->reportingService = $reportingServiceRepo;
        $this->request = $request;
    }

    public function view(): View
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data = $this->reportingService->getReport($this->request);

        $date = date('n') . ' ' . date('Y');

        if ($this->request->month) {
            $date = $months[(int) $this->request->month - 1] . ' ' . $this->request->year;
        }

        if ($this->request->date) {
            $date = $this->request->date;
        }

        $data = $data->map(function ($queue) {
            $queue->shortest_wait_duration = $this->formatTime($queue->shortest_wait_duration);
            $queue->average_wait_duration = $this->formatTime($queue->average_wait_duration);
            $queue->longest_wait_duration = $this->formatTime($queue->longest_wait_duration);
            $queue->shortest_serve_duration = $this->formatTime($queue->shortest_serve_duration);
            $queue->average_serve_duration = $this->formatTime($queue->average_serve_duration);
            $queue->longest_serve_duration = $this->formatTime($queue->longest_serve_duration);

            return $queue;
        });

        return view('adminBranch.excel.service', [
            'branch' => Auth::user()->Branch,
            'date' => $date,
            'department' => Department::find($this->request->department_id),
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
