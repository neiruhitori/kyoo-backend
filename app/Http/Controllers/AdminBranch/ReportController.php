<?php

namespace App\Http\Controllers\AdminBranch;

use Throwable;
use App\Appointment;
use App\DirectQueue;
use App\WorkstationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SurveyQuestions;
use App\Models\SurveyResponses;
use App\Models\AppointmentOnsite;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyConfiguration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
                'service_id' => $request->service_id,
                'workstationServices' => $workstationServices,
                'time_format' => $timeFormat,
                'success' => false
            ]);
        }

        $directQueue = DirectQueue::query()->with(['WorkstationVct','WorkstationVct.user','Vct','subService'])->whereHas('Service', function ($query) {
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
        
        return view('adminBranch.report.directQueue.daily', [
            'directQueues' => $directQueue->get(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status_sort' => $status_sort,
            'service_id' => $request->service_id,
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

    if($request->has('sort') && $request->sort == 'reserve_date'){
        $reserve_date = $request->reservation_date;
        $appointment_onsites = AppointmentOnsite::query()
        ->whereHas('Slot.Service', function ($query) use ($request) {
            if ($request->service_id) {
                $query->where('id', $request->service_id);
            } else {
                $query->where('branch_id', Auth::user()->branch_id);
            }
        })
        ->whereDate('appointment_onsites.date', '>=', $reserve_date)
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
        // dd($filteredAppointments);

            return view('adminBranch.report.directQueue.appointmentOnsite', [
                'appointment_onsites' => $filteredAppointments,
                'start_date' => $start_date,
                'reserve_date' => $reserve_date,
                'end_date' => $end_date,
                'service_id' => $request->service_id,
                'booking_form' => $booking_form,
                'success' => true
            ]);
        }


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
        // dd($filteredAppointments);

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
        $queueType = $this->getUserQueueType();
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $branchId = Auth::user()->Branch->id;
        $config = SurveyConfiguration::where('branch_id', $branchId)->first();
       
        if (!$config) {
            if ($request->filled('survey_type') && $request->survey_type != 'default') {
                    return back()->with('error', 'Survey configuration not found for this branch.');
                }
            $surveyType = 'default';
            $questions = null;
        } else {
            $surveyType = $request->survey_type ?? $config->type;
            $questions = $surveyType != 'default'
                            ? $this->getQuestions($config->id, $config->type)
                            : null;
        }

        if($surveyType == 'nps'){
            $data['reports'] = $questions->isNotEmpty()
                                ? $this->getNPSReport($queueType, $month, $year, $questions[0]->id)
                                : null;
        }elseif($surveyType == 'csat'){
            $responses = $this->getCSATReport($queueType, $month, $year);
            $responseMap = $responses->keyBy('survey_question_id');
            $data['reports'] = $questions->map(function ($q) use ($responseMap) {
                $report = $responseMap->get($q->id);
                    return (object) [
                            'survey_question_id' => $q->id,
                            'question_text'      => $q->question_text,
                            'total_respondent'   => $report ? (int) $report->total_respondent : 0,
                            'avg_score'          => $report ? (float) $report->avg_score : 0,
                            ];
                        });
        }else{
            $data['reports'] = $this->getCustomerSatisfactionReport($queueType, $month, $year);
            foreach ($data['reports'] as $report) {
                $report->date = date('j F Y', strtotime($report->date));
                $report->average_rate = $report->total_feedback 
                    ? $report->total_rating / $report->total_feedback 
                    : 0;
                $report->feedback_percentage = $report->total_queue
                    ? (int) floor($report->total_feedback / $report->total_queue * 100)
                    : 0;
            }
        }        


        $data['month'] = $month;
        $data['year'] = $year;
        $data['surveyType'] = $surveyType;
        $data['questions'] = $questions;

        return view('adminBranch.report.customerSatisfaction', $data);
    }

    private function getQuestions($config_id, $surveyType){
        $questions = SurveyQuestions::where('survey_config_id', $config_id)->select('question_text','question_index', 'id');
        
        if($surveyType == 'nps'){
            $questions->orderBy('question_index', 'asc')->limit(1);
        }

        return $questions->get();
    }

    private function getCustomerSatisfactionReport($queueType, $month, $year){
         $modelMap = [
            'appointment'  => ['model' => Appointment::class, 'dateColumn' => 'date'],
            'direct_queue' => ['model' => DirectQueue::class, 'dateColumn' => 'created_at'],
        ];

        if (!isset($modelMap[$queueType])) {
            return collect();
        }

        $model = $modelMap[$queueType]['model'];
        $dateColumn = $modelMap[$queueType]['dateColumn'];

        $query = $model::select(
                DB::raw($queueType === 'appointment' 
                    ? "{$dateColumn} AS date"
                    : "DATE({$dateColumn}) AS date"),
                DB::raw('COUNT(id) AS total_queue'),
                DB::raw('COUNT(rating) AS total_feedback'),
                DB::raw('SUM(rating) AS total_rating')
            )
            ->whereMonth($dateColumn, $month)
            ->whereYear($dateColumn, $year)
            ->where('branch_id', Auth::user()->branch_id)
            ->where(function ($q) {
                $q->whereNull('survey_type')
                ->orWhere('survey_type', 'default');
            })->where(function ($q) {
                $q->whereNull('rating')
                ->orWhere('rating', '<=', 5);
            });

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    private function getNPSReport($queueType, $month, $year, $question_id){
            $query = SurveyResponses::select(
                DB::raw('COUNT(value) AS total_respondent'),
                DB::raw('SUM(CASE WHEN value BETWEEN 0 AND 6 THEN 1 ELSE 0 END) AS detractors'),
                DB::raw('ROUND((SUM(CASE WHEN value BETWEEN 0 AND 6 THEN 1 ELSE 0 END) / NULLIF(COUNT(value) * 1.0,0)) * 100, 2) AS detractor_percent'),
                DB::raw('SUM(CASE WHEN value BETWEEN 7 AND 8 THEN 1 ELSE 0 END) AS passives'),
                DB::raw('ROUND((SUM(CASE WHEN value BETWEEN 7 AND 8 THEN 1 ELSE 0 END) / NULLIF(COUNT(value) * 1.0,0)) * 100, 2) AS passive_percent'),
                DB::raw('SUM(CASE WHEN value BETWEEN 9 AND 10 THEN 1 ELSE 0 END) AS promoters'),
                DB::raw('ROUND((SUM(CASE WHEN value BETWEEN 9 AND 10 THEN 1 ELSE 0 END) / NULLIF(COUNT(value) * 1.0,0)) * 100, 2) AS promoter_percent'),
                DB::raw('ROUND(AVG(value), 2) AS avg_score')
        )
        ->where('survey_type', 'nps')
        ->where('survey_question_id', $question_id)
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->where('branch_id', Auth::user()->branch_id);

        if ($queueType === 'appointment') {
            $query->whereNotNull('appointment_id');
        } elseif ($queueType === 'direct_queue') {
            $query->whereNotNull('direct_queue_id');
        }

        return $query->first();
    }

    private function getCSATReport($queueType, $month, $year){
        $query = SurveyResponses::select(
                'survey_question_id',
                DB::raw('COUNT(value) AS total_respondent'),
                DB::raw('ROUND(AVG(value * 1.0), 2) AS avg_score')
            )
            ->where('survey_type', 'csat')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('branch_id', Auth::user()->branch_id);

            if ($queueType === 'appointment') {
                $query->whereNotNull('appointment_id');
            } elseif ($queueType === 'direct_queue') {
                $query->whereNotNull('direct_queue_id');
            }

            return $query
                ->groupBy('survey_question_id')
                ->get();
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
