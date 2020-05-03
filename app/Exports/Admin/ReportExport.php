<?php

namespace App\Exports\Admin;

use App\Appointment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    public function view(): View
    {
        $appointments = Appointment::all();
        return view('exports.report', [
            'appointments' => $appointments
        ]);
    }
}
