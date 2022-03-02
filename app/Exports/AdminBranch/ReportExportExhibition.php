<?php

namespace App\Exports\AdminBranch;

use App\Models\Exhibition;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;

class ReportExportExhibition implements FromView
{
    public function view(): View
    {
        $queue = Exhibition::whereHas('Slot.Service', function ($query){
            $query->where('branch_id', Auth::user()->branch_id);
        })
            ->whereMonth('date', date('m'))
            ->get();

        return view('exports.reportExhibition', [
            'queue' => $queue
        ]);
    }
}
