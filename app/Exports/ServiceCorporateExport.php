<?php

namespace App\Exports;

use App\Interfaces\ReportingServiceRepositoryInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceCorporateExport implements FromView
{
    protected $corporate, $date, $branch, $data;

    public function __construct($data)
    {
        $this->corporate = $data['corporate'];
        $this->date = $data['date'];
        $this->branch = $data['branch'];
        $this->data = $data['data'];
    }

    public function view(): View
    {
        return view('adminCorporate.serviceReportExcel', [
            'corporate' => $this->corporate,
            'date' => $this->date,
            'branch' => $this->branch,
            'data' => $this->data
        ]);
    }
}
