<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;

use App\Branch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BranchScheduleTemplateDetail as Holiday;
use App\Schedule;

class HomeController extends Controller
{
    public function index()
    {
        $corporate = Auth::user()->Corporate;

        $branches = Branch::where('corporate_id', $corporate->id)->get();
        $branches = $branches->map(function ($value) {
            return (object) [
                'id' => $value->id,
                'name' => $value->name,
                'open' => $this->getOpenStatus($value),
                'regency' => [
                    'id' => $value->Regency->id,
                    'name' => $value->Regency->name,
                    'lat' => $value->lat ?? $value->Regency->Coordinates->lat,
                    'long' => $value->long ?? $value->Regency->Coordinates->long
                ]
            ];
        });

        return view('adminCorporate.home', [
            'branches' => $branches
        ]);
    }

    protected function getOpenStatus($branch)
    {
        $date = date('Y-m-d');
        $holiday = Holiday::where([
            'branch_id' => $branch->id,
            'date' => $date
        ])->first();

        if ($holiday) {
            return false;
        }

        $day = strtolower(date('l'));

        $closedSchedule = Schedule::where([
            'branch_id' => $branch->id,
            'day' => $day,
            'status' => 'closed',
        ])->first();

        if ($closedSchedule) {
            return false;
        }

        $afterHours = Schedule::where([
            'branch_id' => $branch->id,
            'day' => $day,
            'status' => 'open',
        ])
            ->where('end_time', '<', date('H:i:s'))
            ->orWhere('start_time', '>', date('H:i:s'))
            ->first();

        if ($afterHours) {
            return false;
        }

        return true;
    }
}
