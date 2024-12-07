<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\DirectQueue;
use App\WorkstationService;
use Auth;
use App\Interfaces\ExhibitionRepositoryInterface;
use App\Models\AppointmentOnsite;
use Illuminate\Support\Carbon;
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
        $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
        if ($request->date && date('Y-m-d', strtotime($request->date)) < $last_month) {
            $request->session()->flash('error', __('Can not select report more then last 2 months'));
            return view('cs.report.daily', [
                'appointments' => [],
                'date' => $date,
                'service_id' => $request->service_id,
                'success' => false
            ]);
        }

        $appointments = Appointment::whereHas('Slot.Service', function ($query) use ($request) {
            $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $date)->orderBy('number')->get();

        return view('cs.report.daily', [
            'appointments' => $appointments,
            'date' => $date,
            'service_id' => $request->service_id,
            'success' => true
        ]);
    }

    public function directQueueDaily(Request $request)
    {
        // only can see report within last two months
        $timeFormat = $request->formatTime ?: 'default';
        $start_date = $request->start_date ?: date('Y-m-d');
        $end_date = $request->end_date ?: date('Y-m-d');
        $status_sort = $request->status ?: 'all';

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
            if ($request->start_date && date('Y-m-d', strtotime($request->start_date)) < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 2 months'));
                return view('cs.report.directQueue.daily', [
                    'appointments' => [],
                    'start_date' => $start_date,
                    'time_format' => $timeFormat,
                    'status_sort' => $status_sort,
                    'end_date' => $end_date,
                    'service_id' => $request->service_id,
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
            return view('cs.report.directQueue.daily', [
                'directQueues' => [],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'time_format' => $timeFormat,
                'status_sort' => $status_sort,
                'service_id' => $request->service_id,
                'workstationServices' => $workstationServices,
                'success' => false
            ]);
        }

        $directQueue = DirectQueue::query()->with(['WorkstationVct','WorkstationVct.user'])->whereHas('Service', function ($query) {
            $query->whereBranchId(Auth::user()->branch_id);
        })->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('created_at');


        $directQueue->when($request->service_id, function ($query) use ($request) {
            $query->where('service_id',$request->service_id);
        });
        if($status_sort !== 'all'){
            $directQueue->where('status', $status_sort);
        }
        return view('cs.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'time_format' => $timeFormat,
            'status_sort' => $status_sort,
            'service_id' => $request->service_id,
            'workstationServices' => $workstationServices,
            'success' => true
        ]);
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

        // try {
        //     $params = [
        //         'month' => $request->month ?: date('n'),
        //         'year' => $request->year ?: date('Y'),
        //         'workstation_service_id' => $request->workstation_service_id
        //     ];

        //     $viewData['data'] = DirectQueue::whereHas('Service', function ($query) {
        //         $query->whereBranchId(Auth::user()->branch_id);
        //     })
        //         ->when($params['workstation_service_id'], function ($query) use ($params) {
        //             $query->whereWorkstationServiceId($params['workstation_service_id']);
        //         })
        //         ->whereMonth('created_at', $params['month'])
        //         ->whereYear('created_at', $params['year'])
        //         ->orderBy('created_at')
        //         ->get();

            $viewData['workstationServices'] = WorkstationService::whereHas('Workstation.Department', function ($query) {
                $query->whereBranchId(Auth::user()->branch_id);
            })->get();
        // } catch (Throwable $e) {
        //     $viewData['error'] = $e->getMessage();
        // }

        // if (!Auth::user()->Branch->BranchType->is_premium) {
        //     $last_month = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01 -2 months'));
        //     $request_date = date('Y-m-d', mktime(0, 0, 0, $params['month'], 1, $params['year']));

        //     if ($request_date < $last_month) {
        //         $request->session()->flash('error', __('Can not select report more then last 3 months'));
        //         $viewData['data'] = [];
        //     }
        // }

        return view('cs.report.directQueue.monthly', $viewData);
    }

    public function getDirectQueueMonthly(Request $request)
    {
        $params = [
            'month' => $request->get('month', date('n')),
            'year' => $request->get('year', date('Y')),
            'workstation_service_id' => $request->get('workstation_service_id')
        ];

        $query = DirectQueue::whereHas('Service', function ($query) {
            $query->whereBranchId(Auth::user()->branch_id);
        })
        ->when($params['workstation_service_id'], function ($query) use ($params) {
            $query->whereWorkstationServiceId($params['workstation_service_id']);
        })
        ->whereMonth('created_at', $params['month'])
        ->whereYear('created_at', $params['year'])
        ->orderBy('created_at');

        $totalRecords = $query->count();

        $query->skip($request->start)->take($request->length);

        $data = $query->get()->map(function ($directQueue) {
            return [
                'queue_no' => $directQueue->queue_no,
                'created_at' => date('Y M d H:i:s', strtotime($directQueue->created_at)),
                'called_at' => $directQueue->called_at ? date('Y M d H:i:s', strtotime($directQueue->called_at)) : '-',
                'done_at' => $directQueue->done_at ? date('Y M d H:i:s', strtotime($directQueue->done_at)) : '-',
                'service_time' => $directQueue->done_at ? \Carbon\Carbon::parse($directQueue->done_at)->diffInMinutes(\Carbon\Carbon::parse($directQueue->called_at)) : '-',
                'workstation' => $directQueue->WorkstationService ? $directQueue->WorkstationService->Workstation->name : '',
                'service' => $directQueue->Service->name,
                'service_transfer' => $directQueue->NewService ? $directQueue->NewService->name : '-',
                'status' => __(ucwords($directQueue->status)),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    public function appointmentOnsite(Request $request)
    {
        $date = $request->date ?: date('Y-m-d');
        $booking_form = $request->booking_form ?? 'standard-form';
        $last_month = $newdate = date("Y-m-d", strtotime("-3 months"));
        if ($request->date && date('Y-m-d', strtotime($request->date)) < $last_month) {
            $request->session()->flash('error', __('Can not select report more then last 3 months'));
            return view('cs.report.directQueue.appointment-onsite', [
                'appointment_onsites' => [],
                'date' => $date,
                'service_id' => $request->service_id,
                'success' => false
            ]);
        }

        $appointment_onsites = AppointmentOnsite::whereHas('Slot.Service', function ($query) use ($request) {
                $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
            })
            ->where('date', $date)
            ->join('services', 'appointment_onsites.service_id', '=', 'services.id')
            ->orderBy('date')
            ->orderBy('services.name')
            ->orderBy('start_time')
            ->select('appointment_onsites.*')
            ->get();

        return view('cs.report.directQueue.appointmentOnsite', [
            'appointment_onsites' => $appointment_onsites,
            'date' => $date,
            'service_id' => $request->service_id,
            'booking_form' => $booking_form,
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

        $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
        if ($request->date && (date('Y-m-d', strtotime($request->date)) < $last_month)) {
            $request->session()->flash('error', __('Can not select report more then last 2 months'));
            $viewData['data'] = [];
        }

        return view('cs.report.exhibition.daily', $viewData);
    }

    private function getMonths()
    {
        $months = [];

        for ($i =  0; $i < 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, ($i + 1), 10));
        }

        return $months;
    }
}
