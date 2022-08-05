<?php

namespace App\Exports;

use App\Interfaces\ReportingServiceDistributionRepositoryInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Department;

class ReportingServiceDistributionExport implements FromView
{
    private ReportingServiceDistributionRepositoryInterface $reportingServiceDistribution;

    public function __construct(Request $request, ReportingServiceDistributionRepositoryInterface $reportingServiceDistributionRepo)
    {
        $this->reportingServiceDistribution = $reportingServiceDistributionRepo;
        $this->request = $request;
    }

    public function view(): View
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $data = $this->reportingServiceDistribution->getReport($this->request);

        $date = date('n') . ' ' . date('Y');

        if ($this->request->month) {
            $date = $months[(int) $this->request->month - 1] . ' ' . $this->request->year;
        }

        if ($this->request->date) {
            $date = $this->request->date;
        }

        $data = $data->map(function ($value) {
            $total = 0;

            $total += $value->_0_5;
            $total += $value->_5_10;
            $total += $value->_10_15;
            $total += $value->_15_20;
            $total += $value->_20_25;
            $total += $value->_25_30;
            $total += $value->_30_;

            $value->_0_5_percentage = number_format(($value->_0_5 / $total) * 100, 2) . '%';
            $value->_5_10_percentage = number_format(($value->_5_10 / $total) * 100, 2) . '%';
            $value->_10_15_percentage = number_format(($value->_10_15 / $total) * 100, 2) . '%';
            $value->_15_20_percentage = number_format(($value->_15_20 / $total) * 100, 2) . '%';
            $value->_20_25_percentage = number_format(($value->_20_25 / $total) * 100, 2) . '%';
            $value->_25_30_percentage = number_format(($value->_25_30 / $total) * 100, 2) . '%';
            $value->_30__percentage = number_format(($value->_30_ / $total) * 100, 2) . '%';
            $value->total = $total;

            return $value;
        });

        return view('adminBranch.excel.serviceDistribution', [
            'branch' => Auth::user()->Branch,
            'date' => $date,
            'department' => Department::find($this->request->department_id),
            'data' => $data
        ]);
    }
}
