<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\DirectQueue;
use App\WorkstationService;
use Auth;
use App\Interfaces\ExhibitionRepositoryInterface;

class ReportController extends Controller
{
    private ExhibitionRepositoryInterface $exhibitionRepository;

    public function __construct(ExhibitionRepositoryInterface $exhibitionRepository)
    {
        $this->exhibitionRepository = $exhibitionRepository;
    }

    public function daily(Request $request)
    {
        // only can see report within last two months
        $date = $request->date ?: date('Y-m-d');

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-3 months"));

            if($request->date && date('Y-m-d', strtotime($request->date)) < $last_month){
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                return view('adminBranch.report.daily', [
                    'appointments' => [],
                    'date' => $date,
                    'service_id' => $request->service_id,
                    'success' => false
                ]);
            }
        }

        $appointments = Appointment::whereHas('Slot.Service', function($query) use ($request){
            $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $date)->orderBy('number')->get();
        return view('adminBranch.report.daily', [
            'appointments' => $appointments,
            'date' => $date,
            'service_id' => $request->service_id,
            'success' => true
        ]);
    }

    public function directQueueDaily(Request $request)
    {
        // only can see report within last two months
        $date = $request->date ?: date('Y-m-d');
        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-3 months"));
            if($request->date && date('Y-m-d', strtotime($request->date)) < $last_month){
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                return view('adminBranch.report.directQueue.daily', [
                    'appointments' => [],
                    'date' => $date,
                    'service_id' => $request->service_id,
                    'success' => false
                ]);
            }
        }

        $directQueue = DirectQueue::query()->whereHas('WorkstationService.Service', function($query){
            $query->whereBranchId(Auth::user()->branch_id);
        })->whereDate('created_at', $date)->orderBy('created_at');


        $directQueue->when($request->workstation_service_id, function($query) use ($request) {
            $query->whereWorkstationServiceId($request->workstation_service_id);
        });

        $workstationServices = WorkstationService::whereHas('Workstation.Department', function($query){
            $query->whereBranchId(Auth::user()->branch_id);
        })->get();

        return view('adminBranch.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'date' => $date,
            'workstation_service_id' => $request->workstation_service_id,
            'workstationServices' => $workstationServices,
            'success' => true
        ]);
    }

    public function exhibitionDaily(Request $request)
    {
        $viewData = [
            'data' => [],
            'date' => $request->date ?: date('Y-m-d'),
            'service_id' => $request->service_id
        ];

        try {
            $params = [
                'date' => $request->date ?: date('Y-m-d'),
                'service_id' => $request->service_id,
                'branch_id' => Auth::user()->branch_id
            ];
            
            $viewData['data'] = $this->exhibitionRepository->getDailyReport($params);
        } catch (Throwable $e) {
            $viewData['error'] = $e->getMessage();
        }

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-3 months"));

            if ($request->date && (date('Y-m-d', strtotime($request->date)) < $last_month)) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                $viewData['data'] = [];
            }
        }

        return view('adminBranch.report.exhibition.daily', $viewData);
    }

    private function getMonths()
    {
        $months = [];

        for ($i =  0; $i < 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, ($i + 1), 10));
        }

        return $months;
    }

    public function appointmentMonthly(Request $request)
    {
        $viewData = [
            'data' => [],
            'months' => $this->getMonths(),
            'month' => $request->month ?: date('n'),
            'year' => $request->year ?: date('Y'),
            'service_id' => $request->service_id
        ];

        try {
            $params = [
                'month' => $request->month ?: date('n'),
                'year' => $request->year ?: date('Y'),
                'service_id' => $request->service_id,
                'branch_id' => Auth::user()->branch_id
            ];
            
            $viewData['data'] = Appointment::whereHas('Slot.Service', function($query) use ($params) {
                $params['service_id']
                    ? $query->where('id', $params['service_id'])
                    : $query->where('branch_id', $params['branch_id']);
            })
                ->whereMonth('date', $params['month'])
                ->whereYear('date', $params['year'])
                ->orderBy('created_at')
                ->get();
        } catch (Throwable $e) {
            $viewData['error'] = $e->getMessage();
        }

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01 -2 months'));
            $request_date = date('Y-m-d', mktime(0, 0, 0, $params['month'], 1, $params['year']));

            if ($request_date < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                $viewData['data'] = [];
            }
        }

        return view('adminBranch.report.monthly', $viewData);
    }

    public function directQueueMonthly(Request $request)
    {
        $viewData = [
            'data' => [],
            'months' => $this->getMonths(),
            'month' => $request->month ?: date('n'),
            'year' => $request->year ?: date('Y'),
            'workstation_service_id' => $request->workstation_service_id,
            'workstationServices' => [],
        ];

        try {
            $params = [
                'month' => $request->month ?: date('n'),
                'year' => $request->year ?: date('Y'),
                'workstation_service_id' => $request->workstation_service_id
            ];
            
            $viewData['data'] = DirectQueue::whereHas('WorkstationService.Service', function ($query) {
                $query->whereBranchId(Auth::user()->branch_id);
            })
                ->when($params['workstation_service_id'], function ($query) use ($params) {
                    $query->whereWorkstationServiceId($params['workstation_service_id']);
                })
                ->whereMonth('created_at', $params['month'])
                ->whereYear('created_at', $params['year'])
                ->orderBy('created_at')
                ->get();

            $viewData['workstationServices'] = WorkstationService::whereHas('Workstation.Department', function ($query) {
                $query->whereBranchId(Auth::user()->branch_id);
            })->get();
        } catch (Throwable $e) {
            $viewData['error'] = $e->getMessage();
        }

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01 -2 months'));
            $request_date = date('Y-m-d', mktime(0, 0, 0, $params['month'], 1, $params['year']));

            if ($request_date < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                $viewData['data'] = [];
            }
        }

        return view('adminBranch.report.directQueue.monthly', $viewData);
    }

    public function exhibitionMonthly(Request $request)
    {
        $viewData = [
            'data' => [],
            'months' => $this->getMonths(),
            'month' => $request->month ?: date('n'),
            'year' => $request->year ?: date('Y'),
            'service_id' => $request->service_id
        ];

        try {
            $params = [
                'month' => $request->month ?: date('n'),
                'year' => $request->year ?: date('Y'),
                'service_id' => $request->service_id,
                'branch_id' => Auth::user()->branch_id
            ];
            
            $viewData['data'] = $this->exhibitionRepository->getMonthlyReport($params);
        } catch (Throwable $e) {
            $viewData['error'] = $e->getMessage();
        }

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01 -2 months'));
            $request_date = date('Y-m-d', mktime(0, 0, 0, $params['month'], 1, $params['year']));

            if ($request_date < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                $viewData['data'] = [];
            }
        }

        return view('adminBranch.report.exhibition.monthly', $viewData);
    }
}
