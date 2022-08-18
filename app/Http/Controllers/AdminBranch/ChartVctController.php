<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Interfaces\ReportingVctRepositoryInterface;

class ChartVctController extends Controller
{
    private DirectQueueRepositoryInterface $directQueueRepo;
    private ReportingVctRepositoryInterface $reportingVctRepo;

    public function __construct(
        DirectQueueRepositoryInterface $directQueueRepo,
        ReportingVctRepositoryInterface $reportingVctRepo
    )
    {
        $this->directQueueRepo = $directQueueRepo;
        $this->reportingVctRepo = $reportingVctRepo;
    }

    public function index()
    {
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();

        $id = $departments[0]->id;

        $vcts = User::whereHas('WorkstationVct.Workstation', function ($query) use ($id) {
            $query->where('department_id', $id);
        })->get();

        return view('adminBranch.chart.vct', [
            'departments' => $departments,
            'vcts' => $vcts
        ]);
    }

    public function getAll(Request $request)
    {
        $data = [];

        if ($request->report_type == 'hourly') {
            $data = $this->directQueueRepo->getHourlyQueueByVct($request->vct_id, $request);
        }

        if ($request->report_type == 'daily') {
            $data = $this->reportingVctRepo->getDailyQueueByVct($request->vct_id, $request);
        }

        if ($request->report_type == 'monthly') {
            $data = $this->reportingVctRepo->getMonthlyQueueByVct($request->vct_id, $request);
        }

        return response()->json($data);
    }
}
