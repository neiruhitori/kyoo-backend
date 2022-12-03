<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;

use App\Branch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DirectQueue;

class HomeController extends Controller
{
    public function index()
    {
        $corporate = Auth::user()->Corporate;

        $branches = Branch::onsite()->where('corporate_id', $corporate->id)->get();
        $branches = $branches->map(function ($value) {
            return (object) [
                'id' => $value->id,
                'name' => $value->name,
                'open' => $value->is_today_open,
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
