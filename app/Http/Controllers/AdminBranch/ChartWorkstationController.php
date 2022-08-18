<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;
use App\Workstation;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Interfaces\ReportingWorkstationRepositoryInterface;

class ChartWorkstationController extends Controller
{
    private DirectQueueRepositoryInterface $directQueueRepo;
    private ReportingWorkstationRepositoryInterface $reportingWorkstationRepo;

    public function __construct(
        DirectQueueRepositoryInterface $directQueueRepo,
        ReportingWorkstationRepositoryInterface $reportingWorkstationRepo
    )
    {
        $this->directQueueRepo = $directQueueRepo;
        $this->reportingWorkstationRepo = $reportingWorkstationRepo;
    }

    public function index()
    {
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();
        $workstations = Workstation::where('department_id', $departments[0]->id)->get();

        return view('adminBranch.chart.workstation', [
            'departments' => $departments,
            'workstations' => $workstations
        ]);
    }

    public function getAll(Request $request)
    {
        $data = [];

        if ($request->report_type == 'hourly') {
            $data = $this->directQueueRepo->getHourlyQueueByWorkstation($request->workstation_id, $request);
        }

        if ($request->report_type == 'daily') {
            $data = $this->reportingWorkstationRepo->getDailyQueueByWorkstation($request->workstation_id, $request);
        }

        if ($request->report_type == 'monthly') {
            $data = $this->reportingWorkstationRepo->getMonthlyQueueByWorkstation($request->workstation_id, $request);
        }

        return response()->json($data);
    }
}
