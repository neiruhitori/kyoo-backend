<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\DirectQueue;
use App\WorkstationService;
use App\Interfaces\ExhibitionRepositoryInterface;
use App\Models\AppointmentOnsite;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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

            if ($request->date && date('Y-m-d', strtotime($request->date)) < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                return view('adminBranch.report.daily', [
                    'appointments' => [],
                    'date' => $date,
                    'service_id' => $request->service_id,
                    'success' => false
                ]);
            }
        }

        $appointments = Appointment::whereHas('Slot.Service', function ($query) use ($request) {
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
        $status_sort = $request->status ?: 'all';
        $timeFormat = $request->formatTime ?: 'default';

        $start_date = $request->start_date ?: date('Y-m-d');
        $end_date = $request->end_date ?: date('Y-m-d');
        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-3 months"));
            if ($request->start_date && date('Y-m-d', strtotime($request->start_date)) < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 3 months'));
                return view('adminBranch.report.directQueue.daily', [
                    'directQueues' => [],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status_sort' => $status_sort,
                    'service_id' => $request->service_id,
                    'time_format' => $timeFormat,
                    'success' => false
                ]);
            }
        }

        // Reports can only be within 30 days
        $startDateObj = Carbon::parse($start_date);
        $endDateObj = Carbon::parse($end_date);
        $dateDiff = $endDateObj->diffInDays($startDateObj);

        $workstationServices = WorkstationService::whereHas('Workstation.Department', function ($query) {
            $query->whereBranchId(Auth::user()->branch_id);
        })
        ->select('service_id','id') // Mengambil hanya service_id
        ->distinct('service_id') // Menghindari duplikasi service_id
        ->get();

        if ($dateDiff > 30) {
            $request->session()->flash('error', __('The maximum report selection period is limited to 30 days'));
            return view('adminBranch.report.directQueue.daily', [
                'directQueues' => [],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status_sort' => $status_sort,
                'workstation_service_id' => $request->workstation_service_id,
                'workstationServices' => $workstationServices,
                'time_format' => $timeFormat,
                'success' => false
            ]);
        }

        $directQueue = DirectQueue::query()->with(['WorkstationVct','WorkstationVct.user','Vct'])->whereHas('Service', function ($query) {
            $query->whereBranchId(Auth::user()->branch_id);
        })->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('created_at');

        $directQueue->when($request->workstation_service_id, function ($query) use ($request) {
            $query->whereWorkstationServiceId($request->workstation_service_id);
        });

        if($status_sort !== 'all'){
            $directQueue->where('status', $status_sort);
        }
        
        return view('adminBranch.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status_sort' => $status_sort,
            'workstation_service_id' => $request->workstation_service_id,
            'workstationServices' => $workstationServices,
            'time_format' => $timeFormat,
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

            $viewData['data'] = Appointment::whereHas('Slot.Service', function ($query) use ($params) {
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

            $viewData['data'] = DirectQueue::whereHas('Service', function ($query) {
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

    public function appointmentOnsite(Request $request)
{
    $start_date = $request->start_date ?: date('Y-m-d');
    $end_date = $request->end_date ?: date('Y-m-d');
    $date = $start_date;
    $booking_form = $request->booking_form ?: 'standard-form';

    if (!Auth::user()->Branch->BranchType->is_premium) {
        $last_month = date("Y-m-d", strtotime("-3 months"));
        if ($start_date < $last_month) {
            $request->session()->flash('error', __('Can not select a report more than 3 months ago.'));
            return view('adminBranch.report.directQueue.appointmentOnsite', [
                'appointment_onsites' => [],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'service_id' => $request->service_id,
                'booking_form' => $booking_form,
                'success' => false
            ]);
        }
    }

    $startDateObj = Carbon::parse($start_date);
    $endDateObj = Carbon::parse($end_date);
    $dateDiff = $endDateObj->diffInDays($startDateObj);

    if ($dateDiff > 30) {
        $request->session()->flash('error', __('The maximum report selection period is limited to 30 days.'));
        return view('adminBranch.report.directQueue.appointmentOnsite', [
            'appointment_onsites' => [],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'service_id' => $request->service_id,
            'booking_form' => $booking_form,
            'success' => false
        ]);
    }

    $appointment_onsites = AppointmentOnsite::query()
        ->whereHas('Slot.Service', function ($query) use ($request) {
            if ($request->service_id) {
                $query->where('id', $request->service_id);
            } else {
                $query->where('branch_id', Auth::user()->branch_id);
            }
        })
        ->whereDate('appointment_onsites.created_at', '>=', $start_date)
        ->whereDate('appointment_onsites.created_at', '<=', $end_date)
        ->join('services', 'appointment_onsites.service_id', '=', 'services.id')
        ->orderBy('services.name')
        ->orderBy('start_time')
        ->select('appointment_onsites.*')
        ->get();

        $filteredAppointments = $appointment_onsites->filter(function ($appointment) use ($booking_form) {
            switch ($booking_form) {
                case 'standard-form':
                    return true;
        
                case 'form-medical-1':
                    return !empty($appointment->reason_for_visit) || !empty($appointment->passport_number);
        
                case 'form-financing':
                    return !empty($appointment->contract_number);
        
                default:
                    return true;
            }
        });
        

    return view('adminBranch.report.directQueue.appointmentOnsite', [
        'appointment_onsites' => $filteredAppointments,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'service_id' => $request->service_id,
        'booking_form' => $booking_form,
        'success' => true
    ]);
}


    public function customerSatisfaction(Request $request)
    {
        $queue_type = $this->getUserQueueType();

        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        $data['reports'] = [];
        if ($queue_type == 'appointment') {
            $data['reports'] = Appointment::select(
                'date',
                DB::raw('COUNT(id) AS total_queue'),
                DB::raw('COUNT(rating) AS total_feedback'),
                DB::raw('SUM(rating) AS total_rating')
            )
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('branch_id', Auth::user()->branch_id)
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else if ($queue_type == 'direct_queue') {
            $data['reports'] = DirectQueue::select(
                DB::raw('DATE(created_at) AS date'),
                DB::raw('COUNT(id) AS total_queue'),
                DB::raw('COUNT(rating) AS total_feedback'),
                DB::raw('SUM(rating) AS total_rating')
            )
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('branch_id', Auth::user()->branch_id)
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        foreach ($data['reports'] as $report) {
            $report->date = date('j F Y', strtotime($report->date));
            $report->average_rate = $report->total_feedback ? $report->total_rating / $report->total_feedback : 0;
            $report->feedback_percentage = (int) floor($report->total_feedback / $report->total_queue * 100);
        }

        $data['month'] = $month;
        $data['year'] = $year;

        return view('adminBranch.report.customerSatisfaction', $data);
    }

    private function getUserQueueType()
    {
        $branch_type = Auth::user()->Branch->BranchType;

        $queue_type = 'appointment';

        if ($branch_type->is_direct_queue) {
            $queue_type = 'direct_queue';
        } elseif ($branch_type->is_exhibition) {
            $queue_type = 'exhibition';
        }

        return $queue_type;
    }
}
