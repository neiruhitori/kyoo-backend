<?php

namespace App\Exports\AdminBranch;

use App\Appointment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;

class ReportExport implements FromView
{
    public function view(): View
    {
        $appointments = Appointment::whereHas('Slot.Service', function($query){
            $query->where('branch_id', Auth::user()->branch_id);
        })->whereMonth('date', date('m'))->get();
        return view('exports.report', [
            'appointments' => $appointments
        ]);
    }
}
