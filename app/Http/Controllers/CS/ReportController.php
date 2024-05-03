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
        // only can see report within last two months
        $start_date = $request->start_date ?: date('Y-m-d');
        $end_date = $request->end_date ?: date('Y-m-d');
        if (!Auth::user()->Branch->BranchType->is_premium) {
            $last_month = $newdate = date("Y-m-d", strtotime("-2 months"));
            if ($request->start_date && date('Y-m-d', strtotime($request->start_date)) < $last_month) {
                $request->session()->flash('error', __('Can not select report more then last 2 months'));
                return view('cs.report.directQueue.daily', [
                    'appointments' => [],
                    'start_date' => $start_date,
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
        })->get();

        if ($dateDiff > 30) {
            $request->session()->flash('error', __('The maximum report selection period is limited to 30 days'));
            return view('cs.report.directQueue.daily', [
                'directQueues' => [],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'workstation_service_id' => $request->workstation_service_id,
                'workstationServices' => $workstationServices,
                'success' => false
            ]);
        }

        $directQueue = DirectQueue::query()->whereHas('Service', function ($query) {
            $query->whereBranchId(Auth::user()->branch_id);
        })->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('created_at');


        $directQueue->when($request->workstation_service_id, function ($query) use ($request) {
            $query->whereWorkstationServiceId($request->workstation_service_id);
        });

        return view('cs.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'workstation_service_id' => $request->workstation_service_id,
            'workstationServices' => $workstationServices,
            'success' => true
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
}
