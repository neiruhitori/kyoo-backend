<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;

use App\Branch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BranchScheduleTemplateDetail as Holiday;
use App\Schedule;
use App\DirectQueue;

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
            'totalVisit' => $this->getTotalVisit($branches),
            'totalServed' => $this->getTotalServed($branches),
            'totalNoShow' => $this->getTotalNoShow($branches),
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

    protected function getTotalVisit($branches)
    {
        $branchIds = $branches->map(function ($branch) {
            return $branch->id;
        });

        return DirectQueue::withoutCanceled()
            ->whereHas('Service', function ($query) use ($branchIds) {
                return $query->whereIn('branch_id', $branchIds);
            })
            ->whereDate('created_at', date('Y-m-d H:i:s'))
            ->count();
    }

    protected function getTotalServed($branches)
    {
        $branchIds = $branches->map(function ($branch) {
            return $branch->id;
        });

        return DirectQueue::whereDate('created_at', date('Y-m-d H:i:s'))
            ->whereIn('status', ['served', 'end served'])
            ->whereHas('Service', function ($query) use ($branchIds) {
                return $query->whereIn('branch_id', $branchIds);
            })
            ->count();
    }

    protected function getTotalNoShow($branches)
    {
        $branchIds = $branches->map(function ($branch) {
            return $branch->id;
        });

        return DirectQueue::withoutCanceled()
            ->whereDate('created_at', date('Y-m-d H:i:s'))
            ->where('status', 'no show')
            ->whereHas('Service', function ($query) use ($branchIds) {
                return $query->whereIn('branch_id', $branchIds);
            })
            ->count();
    }
}
