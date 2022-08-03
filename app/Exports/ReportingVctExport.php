<?php

namespace App\Exports;

use App\Interfaces\ReportingVctRepositoryInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Department;
use App\Service;
use App\WorkstationVct;

class ReportingVctExport implements FromView
{
    private ReportingVctRepositoryInterface $reportingVct;

    public function __construct(Request $request, ReportingVctRepositoryInterface $reportingVctRepo)
    {
        $this->reportingVct = $reportingVctRepo;
        $this->request = $request;
    }

    public function view(): View
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data = $this->reportingVct->getReport($this->request);

        $date = date('n') . ' ' . date('Y');

        if ($this->request->month) {
            $date = $months[(int) $this->request->month - 1] . ' ' . $this->request->year;
        }

        if ($this->request->date) {
            $date = $this->request->date;
        }

        $data = $data->map(function ($queue) {
            $workstationId = null;
            if ($worksationVct = WorkstationVct::where('vct_id', $queue->vct_id)->first()) {
                $workstationId = $worksationVct->workstation_id;
            }

            $services = Service::whereHas('WorkstationService', function ($query) use ($workstationId) {
                    $query->where('workstation_id', $workstationId);
                })
                ->get()
                ->map(function ($value) {
                    return $value->name;
                })
                ->join(', ');

            $totalServeDuration = (int) $queue->total_serve_duration;
            $totalOperatingDuration = (int) $queue->total_operating_duration;
            $totalIdleDuration = $totalOperatingDuration - $totalServeDuration;
            $productivityPercentage = ($totalServeDuration / $totalOperatingDuration) * 100;

            $queue->services = $services;
            $queue->shortest_wait_duration = $this->formatTime($queue->shortest_wait_duration);
            $queue->average_wait_duration = $this->formatTime($queue->average_wait_duration);
            $queue->longest_wait_duration = $this->formatTime($queue->longest_wait_duration);
            $queue->shortest_serve_duration = $this->formatTime($queue->shortest_serve_duration);
            $queue->average_serve_duration = $this->formatTime($queue->average_serve_duration);
            $queue->longest_serve_duration = $this->formatTime($queue->longest_serve_duration);
            $queue->total_operating_duration = $this->formatTime($totalOperatingDuration);
            $queue->total_serve_duration = $this->formatTime($totalServeDuration);
            $queue->total_idle_duration = $this->formatTime($totalIdleDuration);
            $queue->productivity_percentage = number_format($productivityPercentage, 2) . '%';

            return $queue;
        });

        return view('adminBranch.excel.vct', [
            'title' => 'Laporan Meja',
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
