<?php

namespace App\Http\Controllers\API;

use App\Slot;
use App\User;
use App\Branch;
use App\Service;
use App\DirectQueue;
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
        $services = Service::where('branch_id', $branch_id)
                            ->get();

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

            $appointmentOnsite = $this->appointmentOnsiteRepository->store($data);

            $branchID = $appointmentOnsite->service->branch_id;

            $branch = Branch::where('id',$branchID)->first();
            $client = BranchConfiguration::where('branch_id',$branchID)->first();
            $tokenAPI = SecretKeyAPi::where('branch_id', $branchID)->first();
            $webhookMessage = "You need an Webhook Url or Activate the feature!";

            if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
                $webhookMessage = "Webhook Send!";
                $webhookData = [
                    'user' => (object)[
                        'id' => $appointmentOnsite->id,
                        'service_id' => $appointmentOnsite->service_id,
                        'name' => $appointmentOnsite->name,
                        'phone' => $appointmentOnsite->phone,
                        'email' => $appointmentOnsite->email,
                        'address' => $appointmentOnsite->address,
                        'passport' => $appointmentOnsite->passport_number,
                        'emergency_contact' => $appointmentOnsite->emergency_number,
                        'reason_for_visit' => $appointmentOnsite->reason_for_visit,
                        'date_of_birth' => $appointmentOnsite->date_of_birth,
                        'created_at' => $appointmentOnsite->created_at,
                    ],
                    'queue' => (object)[
                        'id' => $appointmentOnsite->id,
                        'service_id' => $appointmentOnsite->service_id,
                        'service_name' => $appointmentOnsite->service->name,
                        'service_type' => 'Appointment Onsite Queue',
                        'appointment_date' => $appointmentOnsite->date,
                        'start_time' => $appointmentOnsite->start_time,
                        'end_time' => $appointmentOnsite->end_time,
                        'created_at' => $appointmentOnsite->created_at,
                        'booking_code' => $appointmentOnsite->booking_code,
                        'branch_name' => $branch->name,
                    ]
                ];
                $webhookData = (object) $webhookData;

                $this->sendWebhook($client, $webhookData);
                
            }else{
                $webhookMessage = "There's no Webhook Url/The feature was inactive";
            }

            return response()->json([
                'success' => true,
                'message' => 'appointment onsite created',
                'webhook' => $webhookMessage,
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

    protected function sendWebhook($client, $webhookData)
    {
     
        $guzzle = new \GuzzleHttp\Client();  
        $tokenAPI = SecretKeyAPi::where('branch_id', $client->branch_id)->first();
       

        try {

            $response = $guzzle->post($client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $tokenAPI->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $webhookData
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Webhook failed with status: ' . $response->getStatusCode());
            }

            return response()->json([
                'status' => 'success',
               ]);

        } catch (\Exception $e) {
           return response()->json([
            'status' => 'error',
            'message' =>  $e->getMessage()
           ]);
        }
    }
    
}
