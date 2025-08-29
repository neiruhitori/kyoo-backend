<?php

namespace App\Http\Controllers\API\Mobile;

use Countries;
use App\Branch;
use App\Appointment;
use App\DirectQueue;
use App\Models\Regency;
use App\IndustryCategory;
use App\Models\UserMobile;
use Illuminate\Http\Request;
use App\Models\AppointmentOnsite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function getCategories()
    {
        $categories = IndustryCategory::all();
        return response()->json([
            'message' => 'Get All Categories',
            'data' => $categories,
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
        ]);

        $id = Auth::user()->id;
        UserMobile::where('id',$id)->update($request->all());

        return response()->json([
            'message' => Auth::user(),
        ]); 
    }

    public function getActiveAppointment()
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
            'message' => 'Queue Fetched!',
            'queue_count' => $all->count(),
            'called_queue' => $calledQueue,
            'queue' => $all,
        ]);
    }

    public function getBranchByCategory($categoryId, $regencyId)
    {
        $category_id = $categoryId;
        if (!is_numeric($category_id) || !is_numeric($regencyId)) {
            return response()->json([
                'message' => 'Params must be numeric',
            ], 400);
        }
        $regency = $regencyId ?? Auth::user()->regency;
        $country_user = Auth::user()->country;
        
        if (!$country_user) {
            return response()->json([
                'message' => 'User has not set the country',
                'data' => Auth::user(),
            ], 404);
        }
        $branch = Branch::with(['BranchConfiguration:id,branch_id,layer','BranchType:id,is_appointment,is_direct_queue'])
                            ->where('industry_category_id',$category_id)
                            ->where('country',$country_user)
                            ->where('regency_id',$regency)
                            ->where('is_active',true)
                            ->get()
                            ->reject(function ($b) {
                                return $b->BranchType && $b->BranchType->is_exhibition || $b->BranchConfiguration->layer == 1;
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
                            ]);

        return response()->json([
            'message' => 'Branch Fetched!',
            'data' => $branch,
        ]);
    }

    public function getBranchByRegency($regencyId)
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
        $branch = Branch::with(['BranchConfiguration:id,branch_id,layer','BranchType:id,is_appointment,is_direct_queue'])
                            ->where('regency_id',$regency)
                            ->where('is_active',true)
                            ->get()
                            ->reject(function ($b) {
                                return $b->BranchType && $b->BranchType->is_exhibition || $b->BranchConfiguration->layer == 1;
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
                            ]);

        return response()->json([
            'message' => 'Branch Fetched!',
            'data' => $branch,
        ]);
    }

    public function getPrevBranch()
    {
        $client_id = Auth::user()->client_id;
        if(!$client_id){
            return response()->json([
                'message' => 'Client ID not found',
            ], 404);
        }
        $appointment = Appointment::with(['Branch','Branch.BranchConfiguration:id,branch_id,layer','Branch.BranchType:id,is_appointment,is_direct_queue'])
                                    ->where('client_id', $client_id)
                                    ->where('status', 'end served')
                                    ->latest()
                                    ->take(3)
                                    ->select('branch_id')
                                    ->get()
                                    ->pluck('Branch')
                                    ->unique('id')
                                    ->values()
                                    ->map(function ($b){
                                        return $b->makeHidden([
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
        $directQueue = DirectQueue::with(['Branch','Branch.BranchConfiguration:id,branch_id,layer','Branch.BranchType:id,is_appointment,is_direct_queue'])
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
                                    ->map(function ($b){
                                        return $b->makeHidden([
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

         return response()->json([
            'message' => 'Previous Branch Fetched!',
            'data' => $all,
        ]);
    }
}
