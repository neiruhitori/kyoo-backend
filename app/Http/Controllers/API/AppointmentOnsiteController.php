<?php

namespace App\Http\Controllers\API;

use App\Slot;
use App\User;
use App\Branch;
use App\Service;
use Carbon\Carbon;
use App\DirectQueue;
use App\Jobs\SendWebhook;
use App\WorkstationService;
use App\BranchConfiguration;
use App\Models\SecretKeyAPi;
use Illuminate\Http\Request;
use App\Models\AppointmentOnsite;
use App\Http\Controllers\Controller;
use App\Interfaces\AppointmentOnsiteRepositoryInterface;
use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Http\Resources\AppointmentOnsite\Detail as AppointmentOnsiteDetail;

class AppointmentOnsiteController extends Controller
{
    private AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository;

    public function __construct(AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository)
    {
        $this->appointmentOnsiteRepository = $appointmentOnsiteRepository;
    }

    public function getAllByBranchId(Request $request, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $query = Service::where('branch_id', $branch_id);
        
        if ($request->has('service_category_id') && $request->service_category_id != null) {
            $query->where('service_category_id', $request->service_category_id);
        }
        $services = $query->get();
        foreach ($services as $service) {
            // get filled slot
            $filledSlot = $this->getFilledSlot([
                'service_id' => $service->id,
                'date' => $dateNow
            ]);

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->slots = $slots->get();
            $service->filledSlot = $filledSlot;
            $service->totalSlot = $slots->sum('max_slots');
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data' => $services
        ]);
    }

    public function show(AppointmentOnsite $appointmentOnsite)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail appointment onsite',
            'data' => new AppointmentOnsiteDetail($appointmentOnsite)
        ]);
    }

    public function getFilledSlot($params)
    {
        return AppointmentOnsite::whereHas('Slot', function ($query) use ($params) {
            $query->where('service_id', $params['service_id']);
        })
            ->when(isset($params['slot_id']), function ($q) use ($params) {
                $q->where('slot_id', $params['slot_id']);
            })
            ->where('date', $params['date'])
            ->count();
    }

    public function store(DirectQueueStore $request)
    {
        try {
            Service::find($request->service_id);
            $slot = Slot::find($request->slot_id);

            $data = $request->all();
            $data['phone'] = $this->cleanPhoneNumber($request->phone);
            $data['emergency_number'] = $this->cleanPhoneNumber($request->emergency_number);
            $data['client_id'] = $request->cookie('client_id');
            $data['start_time'] = $slot->start_time;
            $data['end_time'] = $slot->end_time;

            $formattedDate = date('Y-m-d', strtotime($data['date']));
            $email = $phone = '';
        
            if (isset($data['email'])) $email = $data['email'];
            if (isset($data['phone'])) $phone = $data['phone'];
        
            $notUsedAppointment = AppointmentOnsite::where([
                'slot_id' => $data['slot_id'],
                'date' => $formattedDate
            ])
                ->where(function ($query) use ($email, $phone) {
                    $query->where('email', $email)
                          ->orWhere('phone', $phone);
                })
                ->where('is_used', false)
                ->orderBy('created_at', 'desc')
                ->first();
        
            if ($notUsedAppointment) {
                $createdAtRaw = $notUsedAppointment->created_at;
        
                // Konversi ke string jika perlu
                $createdAt = is_string($createdAtRaw) ? $createdAtRaw : $createdAtRaw->toDateTimeString();
        
                $createdAtTimestamp = strtotime($createdAt);
                if ($createdAtTimestamp === false) {
                    throw new \Exception('Invalid created_at format pada appointment, tidak bisa diparse.');
                }
        
                $now = time();
                $diffInMinutes = ($now - $createdAtTimestamp) / 60;
        
                if ($diffInMinutes <= 10) {
                    return response()->json([
                    'success' => true,
                    'message' => 'appointment onsite created',
                    'data' => $notUsedAppointment,
                ]);
                }
            }

            $appointmentOnsite = $this->appointmentOnsiteRepository->store($data);

            $branchID = $appointmentOnsite->service->branch_id;

            $branch = Branch::where('id',$branchID)->first();
            $client = BranchConfiguration::where('branch_id',$branchID)->first();
            $tokenAPI = SecretKeyAPi::where('branch_id', $branchID)->first();
            $webhookMessage = "You need an Webhook Url or Activate the feature!";

            if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
                $webhookMessage = "Webhook Send!";

                $startTime = Carbon::createFromFormat('H:i', $appointmentOnsite->start_time)->format('H:i:s');
                $endTime = Carbon::createFromFormat('H:i',$appointmentOnsite->end_time)->format('H:i:s');
                $timezone = null;
                if($branch && $branch->timezone){
                    if($branch && $branch->timezone) {
                        switch($branch->timezone) {
                            case 'WIB':
                                $timezone = 'GMT+7';
                                break;
                            case 'WITA':
                                $timezone = 'GMT+8';
                                break;
                            case 'WIT':
                                $timezone = 'GMT+9';
                                break;
                            default:
                                $timezone = null;
                                break;
                        }
                    }
                }


                $webhookData = [
                    'event_type' => 'onsite_create_booking',

                    'user' =>[
                        'queue_id' => $appointmentOnsite->id,
                        'service_id' => $appointmentOnsite->service_id,
                        'name' => $appointmentOnsite->name,
                        'phone' => $appointmentOnsite->phone,
                        'email' => $appointmentOnsite->email ?? "No email",
                        'address' => $appointmentOnsite->address ?? "No address",
                        'customer_id' => $appointmentOnsite->passport_number,
                        'emergency_contact' => $appointmentOnsite->emergency_number,
                        'reason_for_visit' => $appointmentOnsite->reason_for_visit,
                        'date_of_birth' => $appointmentOnsite->date_of_birth,
                        'created_at' => $appointmentOnsite->created_at,
                    ],
                    'queue' =>[
                        'id' => $appointmentOnsite->id,
                        'service_id' => $appointmentOnsite->service_id,
                        'branch_id' =>  $branchID,
                        'booking_code' =>  strtoupper($appointmentOnsite->booking_code),
                        'service_type' => 'Appointment Onsite Queue',
                        'queue_date' => $appointmentOnsite->date,
                        'timezone' => $timezone,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'created_at' => $appointmentOnsite->created_at,
                    ],
                    'branch' =>[
                        'id' =>  $branchID,
                        'name' => $branch->name,
                    ],
                    'service' =>[
                        'id' => $appointmentOnsite->service_id,
                        'name' => $appointmentOnsite->service->name,
                        'branch_id' => $appointmentOnsite->service->Branch->id,
                        'branch_name' => $appointmentOnsite->service->Branch->name,
                    ]
                ];
               SendWebhook::dispatch($client, $webhookData);
                
            }else{
                $webhookMessage = "There's no Webhook Url/The feature was inactive";
            }

            return response()->json([
                'success' => true,
                'message' => 'appointment onsite created',
                'webhook' =>  $webhookMessage,
                'data' => $appointmentOnsite,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }

    private function cleanPhoneNumber($phoneNumber) {
        $cleaned_phone = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (substr($cleaned_phone, 0, 1) == '0') {
            $cleaned_phone = '62' . substr($cleaned_phone, 1);
        } elseif (substr($cleaned_phone, 0, 3) == '620') {
            $cleaned_phone = '62' . substr($cleaned_phone, 3);
        } elseif (substr($cleaned_phone, 0, 1) == '8') {
            $cleaned_phone = '628' . substr($cleaned_phone, 1);
        }

        return $cleaned_phone;
    }
    
}
