<?php

namespace App\Exports\AdminBranch;

use App\Appointment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    public function view(): View
    {
        $appointments = Appointment::whereMonth('date', date('m'))->get();
        return view('exports.report', [
            'appointments' => $appointments
        ]);
    }
}
