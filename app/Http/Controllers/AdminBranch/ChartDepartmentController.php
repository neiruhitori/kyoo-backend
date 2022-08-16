<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Department;
use Illuminate\Support\Facades\Auth;

class ChartDepartmentController extends Controller
{
    private DirectQueueRepositoryInterface $directQueueRepo;

    public function __construct(DirectQueueRepositoryInterface $directQueueRepo)
    {
        $this->directQueueRepo = $directQueueRepo;
    }

    public function index()
    {
        return view('adminBranch.chart.department', [
            'departments' => Department::where('branch_id', Auth::user()->branch_id)->get()
        ]);
    }

    public function getAll(Request $request)
    {
        $data = [];

        if ($request->report_type == 'hourly') {
            $data = $this->directQueueRepo->getHourlyQueueByDepartment($request->department_id, $request);
        }

        if ($request->report_type == 'daily') {
            $data = $this->directQueueRepo->getDailyQueueByDepartment($request->department_id, $request);
        }

        if ($request->report_type == 'monthly') {
            $data = $this->directQueueRepo->getMonthlyQueueByDepartment($request->department_id, $request);
        }

        return response()->json($data);
    }
}
