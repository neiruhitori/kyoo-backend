<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;
use App\Service;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Interfaces\ReportingServiceRepositoryInterface;

class ChartServiceController extends Controller
{
    private DirectQueueRepositoryInterface $directQueueRepo;
    private ReportingServiceRepositoryInterface $reportingServiceRepo;

    public function __construct(
        DirectQueueRepositoryInterface $directQueueRepo,
        ReportingServiceRepositoryInterface $reportingServiceRepo
    )
    {
        $this->directQueueRepo = $directQueueRepo;
        $this->reportingServiceRepo = $reportingServiceRepo;
    }

    public function index()
    {
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();
        $services = Service::where('department_id', $departments[0]->id)->get();

        return view('adminBranch.chart.service', [
            'departments' => $departments,
            'services' => $services
        ]);
    }

    public function getAll(Request $request)
    {
        $data = [];

        if ($request->report_type == 'hourly') {
            $data = $this->directQueueRepo->getHourlyQueueByService($request->service_id, $request);
        }

        if ($request->report_type == 'daily') {
            $data = $this->reportingServiceRepo->getDailyQueueByService($request->service_id, $request);
        }

        if ($request->report_type == 'monthly') {
            $data = $this->reportingServiceRepo->getMonthlyQueueByService($request->service_id, $request);
        }

        return response()->json($data);
    }
}
