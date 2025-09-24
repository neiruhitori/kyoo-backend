<?php

namespace App\Http\Controllers\API\Mobile;

use App\Slot;
use Countries;
use App\Branch;
use App\Service;
use App\Appointment;
use App\DirectQueue;
use App\Models\Regency;
use App\IndustryCategory;
use App\Models\UserMobile;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\AppointmentOnsite;
use App\Helpers\FormBookingHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function getRegion()
    {
        $countries = Countries::getList('en_US');
        $selectedCountries = array_intersect_key($countries, array_flip(['ID','SG','VN','MY','TL','BN','TH','AU','QA','SA','KW','OM','AE','NZ','US']));
        return response()->json([
            'message' => 'Get All Country',
            'data' => $selectedCountries,
        ]);
    }

    public function getCategories(Request $request)
    {
        $perPage = $request->get('per_page', 10) ?? 10;
        $page    = $request->get('page', 1) ?? 1;

        $paginator = IndustryCategory::paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Get All Categories',
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ]
        ]);
    }
    
    public function detail()
    {
        return response()->json(Auth::user());
    }

    public function setRegion(Request $request)
    {
        $request->validate([
            'country' => 'string|required',
            'regency' => 'required',
        ],[
            'required' => ':attribute is required',
        ]);

        $user = Auth::user();
        UserMobile::where('id',$user->id)->update($request->all());

        return response()->json([
            'message' => 'Region Changed!',
            'data' => [
                        'country' => $user->country,
                        'regency' =>[ 
                                    'id' => $user->Regency ? $user->Regency->id : null, 
                                    'name' => $user->Regency ? $user->Regency->name : null 
                                    ],
                    ]
        ]); 
    }

    public function getActiveAppointment(Request $request)
    {
        $client_id = Auth::user()->client_id; 

        $appointments = Appointment::with([
                                    'Service' => function ($q) {
                                        $q->select('id', 'branch_id', 'name');
                                    },
                                    'Branch' => function ($q) {
                                        $q->select('id', 'name', 'address', 'country', 'regency_id');
                                    }
                                ])
                                ->where('client_id', $client_id)
                                ->where('date','>=', now())
                                ->whereNotIn('status',['end served', 'no show'])
                                ->orderBy('date','asc')
                                ->get();

        $appointments->each(function ($appointment) {
            $appointment->is_appointment = true;
            $appointment->is_onsite_hybrid = false;
            $appointment->is_direct_queue = false;
                if ($appointment->Branch) {
                    $appointment->Branch->makeHidden(['is_today_open', 'holiday', 'schedule']);
                }
            });

        $appointment_onsites = AppointmentOnsite::with([
                                    'Service' => function ($q) {
                                        $q->select('id', 'branch_id', 'name');
                                    },
                                    'Service.Branch' => function ($q) {
                                        $q->select('id', 'name', 'address', 'country', 'regency_id');
                                    }
                                ])
                                ->where('client_id', $client_id)
                                ->where('is_used', false)
                                ->where('date','>=', now())
                                ->orderBy('date','asc')
                                ->get();

        $appointment_onsites->each(function ($appointment_onsite) {
            $appointment_onsite->is_appointment = false;
            $appointment_onsite->is_onsite_hybrid = true;
            $appointment_onsite->is_direct_queue = false;
                if ($appointment_onsite->Service && $appointment_onsite->Service->Branch) {
                    $appointment_onsite->Service->Branch->makeHidden(['is_today_open', 'holiday', 'schedule']);
                }
            });
        
        $direct_queues = DirectQueue::with([
                                    'Service' => function ($q) {
                                        $q->select('id', 'branch_id', 'name');
                                    },
                                    'Branch' => function ($q) {
                                        $q->select('id', 'name', 'address', 'country', 'regency_id');
                                    }
                                ])
                                ->where('client_id', $client_id)
                                ->whereNotIn('status',['end served', 'no show'])
                                ->whereDate('created_at', now()->toDateString())
                                ->get();

        $direct_queues->each(function ($direct_queue) {
            $direct_queue->is_appointment = false;
            $direct_queue->is_onsite_hybrid = false;
            $direct_queue->is_direct_queue = true;
                if ($direct_queue->Branch) {
                    $direct_queue->Branch->makeHidden(['is_today_open', 'holiday', 'schedule']);
                }
            });

        $all = collect()
            ->merge($appointments)
            ->merge($appointment_onsites)
            ->merge($direct_queues)
            ->sortBy('date')
            ->take(8)
            ->values();

        $perPage = $request->get('per_page', 8) ?? 8; 
        $page    = $request->get('page', 1) ?? 1;      
        $offset  = ($page - 1) * $perPage;

        $paginated = new LengthAwarePaginator(
            $all->slice($offset, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $calledQueue = collect()
                    ->merge(
                        $direct_queues->filter(fn($dq) => $dq->status === 'served' && $dq->call_time !== null)
                    )
                    ->merge(
                        $appointments->filter(fn($ap) => $ap->status === 'served' && $ap->served_time !== null)
                    )
                    ->sortByDesc(fn($item) => $item->call_time ?? $item->served_time)
                    ->first();
                    

        return response()->json([
            'message'      => 'Queue Fetched!',
            'queue'        => $paginated->items(),
            'called_queue' => $calledQueue,
            'total'        => $paginated->total(),
            'page'         => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ]);
    }

    public function getBranchByCategory(Request $request, $categoryId, $regencyId)
    {
        $category_id = $categoryId;
        if (!is_numeric($category_id) || !is_numeric($regencyId)) {
            return response()->json([
                'message' => 'Params must be numeric',
            ], 400);
        }
        $regency = $regencyId ?? Auth::user()->regency;
        
        $today = strtolower(now()->format('l'));
        $query = Branch::with(['BranchConfiguration:id,branch_id,layer',
                                'BranchType:id,is_appointment,is_direct_queue',
                                'IndustryCategory:id,name'
                            ])
                            ->where('industry_category_id',$category_id)
                            ->where('regency_id',$regency)
                            ->where('is_active',true);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        }
        $branch = $query->get()
                        ->reject(function ($b) {
                                if (!$b->BranchConfiguration || $b->BranchConfiguration->layer === null) {
                                    return true;
                                }
                                if ($b->BranchType && $b->BranchType->is_exhibition) {
                                    return true;
                                }
                                if ($b->BranchType && $b->BranchType->is_direct_queue) {
                                    return $b->BranchConfiguration->layer != 2;
                                }
                                return false;
                            //  return ($b->BranchType && $b->BranchType->is_exhibition)
                            //         || ($b->BranchConfiguration && $b->BranchConfiguration->layer !== null && $b->BranchConfiguration->layer == 1);
                        })
                        ->makeHidden([
                                'fixed_phone',
                                'mobile_phone',
                                'lat',
                                'long',
                                'likes',
                                'max_queue',
                                'max_counter',
                                'corporate_id',
                                'license_expiration_date',
                            ])->map(function ($branch) use ($today) {
                                $arr = $branch->toArray();

                                if (isset($arr['branch_type'])) {
                                    $arr['branch_type']['is_onsite_hybrid'] = 
                                        ($branch->BranchType && $branch->BranchType->is_direct_queue)
                                        && ($branch->BranchConfiguration && $branch->BranchConfiguration->layer == 2);
                                } else {
                                    $arr['branch_type'] = [
                                        'is_onsite_hybrid' => false
                                        ];
                                }

                                if (isset($arr['schedule'])) {
                                    $arr['schedule'] = collect($arr['schedule'])
                                                        ->where('day', $today)
                                                        ->values()
                                                        ->all();
                                }
                                return $arr;
                            });
            $perPage = $request->get('per_page', 10) ?? 10;
            $page    = $request->get('page', 1) ?? 1;    

            $paginated = new LengthAwarePaginator(
                $branch->forPage($page, $perPage)->values(),
                $branch->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        return response()->json([
            'message' => 'Branch Fetched!',
            'data'    => $paginated->items(),
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ]
        ]);
    }

    public function getBranchByRegency(Request $request,$regencyId)
    {
        if (!is_numeric($regencyId)) {
            return response()->json([
                'message' => 'Params must be numeric',
            ], 400);
        }
        $regency = $regencyId ?? Auth::user()->regency;
        
        if (!$regency) {
            return response()->json([
                'message' => 'User has not set the regency',
                'data' => Auth::user(),
            ], 404);
        }

        $query = Branch::with(['BranchConfiguration:id,branch_id,layer',
                                'BranchType:id,is_appointment,is_direct_queue',
                                'IndustryCategory:id,name'])
                            ->where('regency_id',$regency)
                            ->where('is_active',true);
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        }
        $today = strtolower(now()->format('l'));
        $branch = $query->get()
                            ->reject(function ($b) {
                                if (!$b->BranchConfiguration || $b->BranchConfiguration->layer === null) {
                                    return true;
                                }
                                if ($b->BranchType && $b->BranchType->is_exhibition) {
                                    return true;
                                }
                                if ($b->BranchType && $b->BranchType->is_direct_queue) {
                                    return $b->BranchConfiguration->layer != 2;
                                }
                                return false;

                                // return ($b->BranchType && $b->BranchType->is_exhibition)
                                //     || ($b->BranchConfiguration && $b->BranchConfiguration->layer !== null && $b->BranchConfiguration->layer == 1);
                            })
                            ->makeHidden([
                                'fixed_phone',
                                'mobile_phone',
                                'lat',
                                'long',
                                'likes',
                                'max_queue',
                                'max_counter',
                                'corporate_id',
                                'license_expiration_date',
                            ])->map(function ($branch) use ($today) {
                                $arr = $branch->toArray();

                                if (isset($arr['branch_type'])) {
                                    $arr['branch_type']['is_onsite_hybrid'] = 
                                        ($branch->BranchType && $branch->BranchType->is_direct_queue)
                                        && ($branch->BranchConfiguration && $branch->BranchConfiguration->layer == 2);
                                } else {
                                    $arr['branch_type'] = [
                                        'is_onsite_hybrid' => false
                                    ];
                                }

                                if (isset($arr['schedule'])) {
                                    $arr['schedule'] = collect($arr['schedule'])
                                                        ->where('day', $today)
                                                        ->values()
                                                        ->all();
                                }
                                return $arr;
                            });

            $perPage = $request->get('per_page', 10) ?? 10;
            $page    = $request->get('page', 1) ?? 1;    

            $paginated = new LengthAwarePaginator(
                $branch->forPage($page, $perPage)->values(),
                $branch->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        return response()->json([
            'message' => 'Branch Fetched!',
            'data'    => $paginated->items(),
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ]
        ]);
    }

    public function getPrevBranch(Request $request)
    {
        $client_id = Auth::user()->client_id;
        if(!$client_id){
            return response()->json([
                'message' => 'Client ID not found',
            ], 404);
        }
        $today = strtolower(now()->format('l'));
        $appointment = Appointment::with(['Branch',
                                    'Branch.BranchConfiguration:id,branch_id,layer',
                                    'Branch.BranchType:id,is_appointment,is_direct_queue',
                                    'Branch.IndustryCategory:id,name'])
                                    ->where('client_id', $client_id)
                                    ->where('status', 'end served')
                                    ->latest()
                                    ->take(3)
                                    ->select('branch_id')
                                    ->get()
                                    ->pluck('Branch')
                                    ->unique('id')
                                    ->values()
                                    ->map(function ($b) use ($today){
                                        $arr = $b->toArray();

                                        if (isset($arr['branch_type'])) {
                                            $arr['branch_type']['is_onsite_hybrid'] = 
                                                ($b->BranchType && $b->BranchType->is_direct_queue)
                                                && ($b->BranchConfiguration && $b->BranchConfiguration->layer == 2);
                                        } else {
                                            $arr['branch_type'] = [
                                                'is_onsite_hybrid' => false
                                            ];
                                        }

                                        if (isset($arr['schedule'])) {
                                            $arr['schedule'] = collect($arr['schedule'])
                                                                ->where('day', $today)
                                                                ->values()
                                                                ->all();
                                        }
                                        return collect($arr)->except([
                                            'fixed_phone',
                                            'mobile_phone',
                                            'lat',
                                            'long',
                                            'likes',
                                            'max_queue',
                                            'max_counter',
                                            'corporate_id',
                                            'license_expiration_date',
                                        ]);
                                    }); 

        $directQueue = DirectQueue::with([  'Branch',
                                            'Branch.BranchConfiguration:id,branch_id,layer',
                                            'Branch.BranchType:id,is_appointment,is_direct_queue',
                                            'Branch.IndustryCategory:id,name'])
                                    ->where('client_id', $client_id)
                                    ->whereNotNull('appointment_onsite_id')
                                    ->where('status', 'end served')
                                    ->latest()
                                    ->take(3)
                                    ->select('branch_id')
                                    ->get()
                                    ->pluck('Branch')
                                    ->unique('id')
                                    ->values()
                                    ->map(function ($b) use ($today){
                                        $arr = $b->toArray();

                                        if (isset($arr['branch_type'])) {
                                            $arr['branch_type']['is_onsite_hybrid'] = 
                                                ($b->BranchType && $b->BranchType->is_direct_queue)
                                                && ($b->BranchConfiguration && $b->BranchConfiguration->layer == 2);
                                        } else {
                                            $arr['branch_type'] = [
                                                'is_onsite_hybrid' => false
                                            ];
                                        }

                                        if (isset($arr['schedule'])) {
                                            $arr['schedule'] = collect($arr['schedule'])
                                                                ->where('day', $today)
                                                                ->values()
                                                                ->all();
                                        }
                                        return collect($arr)->except([
                                            'fixed_phone',
                                            'mobile_phone',
                                            'lat',
                                            'long',
                                            'likes',
                                            'max_queue',
                                            'max_counter',
                                            'corporate_id',
                                            'license_expiration_date',
                                        ]);
                                    });
            
            $all = collect()
            ->merge($appointment)
            ->merge($directQueue)
            ->take(6)
            ->values();
            
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $all = $all->filter(function ($branch) use ($search) {
                    return strpos(strtolower($branch->name), $search) !== false;
                })->values();
            }
            $perPage = $request->get('per_page', 6) ?? 6;
            $page = $request->get('page', 1) ?? 1;

            $paginated = new LengthAwarePaginator(
                $all->forPage($page, $perPage)->values(),
                $all->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );


         return response()->json([
            'message' => 'Previous Branch Fetched!',
            'data'    => $paginated->items(),
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ]
        ]);
    }

    public function regencyByProvince(Request $request, $country, $province_id = null){
        $query = Regency::where('country', $country);

        if (!empty($province_id)) {
            $query->where('province_id', $province_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        }
        $perPage = $request->get('per_page', 10) ?? 10;
        $page    = $request->get('page', 1) ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'get all regencies',
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ]
        ]);
    }

    public function getServiceCategoryBranch(Request $request, $branch_id)
    {
        $service_categories = ServiceCategory::where('branch_id', $branch_id);

        $perPage = $request->get('per_page', 10) ?? 10;
        $page    = $request->get('page', 1) ?? 1;

        $paginator = $service_categories->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'success' => true,
            'message' => 'get all service categories by branch id',
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ]
        ]);
    }

    public function getServiceByBranchId(Request $request, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $branch = Branch::find($branch_id);
        $booking_form = $branch->BranchConfiguration->template_booking_form;
        $type = [
                'appointment' => 'appointment',
                'onsite' => 'appointment-onsite',
        ];
        $queueType = $type[$branch->queue_type];

        $query = Service::where('branch_id', $branch_id)
                            ->where('is_disable',false);

        if ($request->filled('service_category_id')) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $perPage = $request->get('per_page', 10) ?? 10;
        $page    = $request->get('page', 1) ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
                            
        $services = $paginator->items();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = $this->getFilledSlot($queueType, [
                'service_id' => $service->id,
                'date' => $dateNow
            ]);

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->template_form_booking = $service->template_form_booking ?? $booking_form;
            $service->slots = $slots->get();
            $service->filledSlot = $filledSlot;
            $service->totalSlot = $slots->sum('max_slots');
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ]
        ]);
    }

    public function getSlot(Request $request, $id)
    {
        $date = $request->date ?? null;

        $service = Service::with(['Branch'])
            ->where('id', $id)
            ->firstOrFail()
            ->makeHidden('Branch');

        if ($date) {
            $day = strtolower(date("l", strtotime($date)));
            $slots = $service->Slot()->where('day', $day)->get();
        } else {
            $slots = $service->Slot;
        }

        $type = [
            'appointment' => 'appointment',
            'onsite' => 'appointment-onsite',
        ];

        $queueType = $request->queue_type ?? $type[$service->Branch->queue_type];
        $formBooking = $service->template_form_booking ?? $service->Branch->BranchConfiguration->template_booking_form;

        foreach ($slots as $slot) {
            $slot->filled_slot = $this->getFilledSlot($queueType, [
                'service_id' => $service->id,
                'slot_id'    => $slot->id,
                'date'       => $date ?? date('Y-m-d') 
            ]);
        }

        $service->setRelation('Slot', $slots);

        $service['template_form_booking'] = $formBooking;
        $service['fields'] = FormBookingHelper::getForm($formBooking, $queueType);

        return response()->json([
            'success' => true,
            'message' => 'get service by id',
            'data'    => $service
        ]);
    }

    public function popularBranch(Request $request)
    {
        $startDate = now()->subMonth();
        $endDate   = now();
        $today     = strtolower(now()->format('l'));
        $regencyId = $request->get('regency_id') ?? null;

        $union = DB::table('appointments')
            ->select('service_id', 'rating')
            ->where('status', 'end served')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->unionAll(
                DB::table('direct_queues')
                    ->select('service_id', 'rating')
                    ->where('status', 'end served')
                    ->whereBetween('created_at', [$startDate, $endDate])
            );

            $branches = Branch::with([
                    'Schedule' => fn($q) => $q->where('day', $today),
                    'IndustryCategory:id,name',
                    'BranchType:id,is_appointment,is_direct_queue',
                    'BranchConfiguration:id,branch_id,layer'
                ])
                ->join('services as s', 's.branch_id', '=', 'branches.id')
                ->joinSub($union, 'q', fn($join) => $join->on('q.service_id', '=', 's.id'))
                ->selectRaw('branches.*, COUNT(q.service_id) as total_queue, AVG(q.rating) as avg_rating')
                ->when($regencyId, fn($q) => $q->where('branches.regency_id', $regencyId))
                ->groupBy('branches.id')
                ->orderByDesc('total_queue')
                ->orderByDesc('avg_rating')
                ->limit(10)
                ->get()
                ->reject(function ($b) {
                    if (!$b->BranchConfiguration || $b->BranchConfiguration->layer === null) {
                        return true;
                    }
                    if ($b->BranchType && $b->BranchType->is_exhibition) {
                        return true;
                    }
                    if ($b->BranchType && $b->BranchType->is_direct_queue) {
                        return $b->BranchConfiguration->layer != 2;
                    }
                    return false;
                })
                ->map(function ($b) {
                    $arr = $b->toArray();

                    if (isset($arr['branch_type'])) {
                        $arr['branch_type']['is_onsite_hybrid'] = 
                            ($b->BranchType && $b->BranchType->is_direct_queue)
                            && ($b->BranchConfiguration && $b->BranchConfiguration->layer == 2);
                    } else {
                        $arr['branch_type'] = [
                            'is_onsite_hybrid' => false
                            ];
                    }

                    return $arr;
                })
                ->values();

        return response()->json([
            'success' => true,
            'message' => 'Popular branch this month',
            'data' => $branches
        ]);
    }

    public function getFilledSlot($queue_type, $params)
    {
        if ($queue_type == 'appointment') {
            return Appointment::withoutCanceled()->whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }

        if ($queue_type == 'exhibition') {
            return Exhibition::whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }

        if($queue_type == 'appointment-onsite') {
            return AppointmentOnsite::whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }
    }

}
