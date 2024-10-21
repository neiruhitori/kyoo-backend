<?php

namespace App\Http\Controllers\AdminBranch;

use Auth;
use App\Slot;
use App\Service;
use Carbon\Carbon;
use App\BranchConfiguration;
use App\Models\SecretKeyAPi;
use Illuminate\Http\Request;
use App\Models\AppointmentOnsite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\CS\AppointmentOnsiteCreatedMail;

class AppointmentOnsiteController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?: date('Y-m-d');

        $appointment_onsites = AppointmentOnsite::whereHas('Slot.Service', function ($query) use ($request) {
                                                    $request->service_id ? $query->where('id', $request->service_id) : $query->where('branch_id', Auth::user()->branch_id);
                                                })->where('date', $date)
                                                ->where('is_used', false)
                                                ->join('services', 'appointment_onsites.service_id', '=', 'services.id')
                                                ->orderBy('date')
                                                ->orderBy('services.name')
                                                ->orderBy('start_time')
                                                ->select('appointment_onsites.*')
                                                ->get();

        return view('adminBranch.appointmentOnsites.index', [
            'appointment_onsites' => $appointment_onsites,
            'date' => $date,
            'success' => true
        ]);
    }

    public function editSlot(AppointmentOnsite $appointmentOnsite)
    {
        $services = Service::where('branch_id', Auth::user()->branch_id)->get();

        return view('adminBranch.appointmentOnsites.editSlot', [
            'appointment_onsite' => $appointmentOnsite,
            'services' => $services
        ]);
    }

    public function update(AppointmentOnsite $appointmentOnsite, Request $request)
    {
        $branch = Auth::user()->Branch;
        $slot = Slot::find($request->slot_id);

        if ($this->isAppointmentSlotFull($request->slot_id, $request->date)) {
            return redirect()->back()->with('error', 'Sesi Appointment Tidak Tersedia');
        }
        $branchID = Auth::user()->branch_id;

        $client = BranchConfiguration::where('branch_id',$branchID)->first();
        $tokenAPI = SecretKeyAPi::where('branch_id', $branchID)->first();
        $webhookMessage = "You need an Webhook Url or Activate the feature!";

        if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
            $webhookMessage = "Webhook Send!";
            $startTime = Carbon::createFromFormat('H:i:s', $appointmentOnsite->start_time)->format('H:i:s');
            $endTime = Carbon::createFromFormat('H:i:s',$appointmentOnsite->end_time)->format('H:i:s');
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
                'event_type' => 'onsite_modify_booking',

                'user' => (object)[
                    'appointment_id' => $appointmentOnsite->id,
                    'service_id' => $appointmentOnsite->service_id,
                    'name' => $appointmentOnsite->name,
                    'phone' => $appointmentOnsite->phone,
                    'email' => $appointmentOnsite->email,
                    'created_at' => $appointmentOnsite->created_at,
                ],
                'queue' => (object)[
                    'id' => $appointmentOnsite->id,
                    'service_id' => $appointmentOnsite->service_id,
                    'service_name' => $appointmentOnsite->service->name,
                    'service_type' => 'Appointment Onsite Queue',
                    'appointment_date' => $appointmentOnsite->date,
                    'timezone' => $timezone,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'created_at' => $appointmentOnsite->created_at,
                    'booking_code' =>  strtoupper($appointmentOnsite->booking_code),
                    'branch_id' => $branchID,
                    'branch_name' => $branch->name,
                ]
            ];
            $webhookUpdatedData = (object) $webhookData;

           $this->sendWebhook($client, $webhookUpdatedData);
            
        }else{
            $webhookMessage = "There's no Webhook Url/The feature was inactive";
        }

        $appointmentOnsite->update([
            'service_id' => $request->service_id,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'date' => $request->date,
            'slot_id' => $request->slot_id,
        ]);

        if (
            $appointmentOnsite->phone &&
            $branch &&
            $branch->is_premium &&
            $branch->BranchConfiguration->wa_notification != false &&
            $branch->BranchConfiguration->whatsapp_type == 'official_wa_branch'
        ) {
            $appointmentOnsite->sendAppointmentOnsiteCreatedNotification($appointmentOnsite);
        }

        try {
            Mail::to($appointmentOnsite->email)->send(new AppointmentOnsiteCreatedMail($appointmentOnsite));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Pengiriman email gagal, mohon cek koneksi internet Anda.');
        }

        return redirect()->back()->with('success', 'Slot Waktu diperbaharui');
    }

    public function getSlots(Request $request)
    {
        $slots = Slot::where('service_id', $request->service_id)->where('day', $request->day)->get();

        return response()->json([
            'slots' => $slots,
            'status' => 'success',
        ]);
    }

    public function isAppointmentSlotFull($slotId, $date)
    {
        $slot = Slot::find($slotId);
        $formattedDate = date('Y-m-d', strtotime($date));

        $totalTodayAppointmentsBySlot = AppointmentOnsite::where([
            'slot_id' => $slotId,
            'date' => $formattedDate
        ])->count();

        return $totalTodayAppointmentsBySlot >= $slot->max_slots;
    }

    protected function sendWebhook($client, $webhookUpdatedData)
    {
     
        $guzzle = new \GuzzleHttp\Client();  
        $tokenAPI = SecretKeyAPi::where('branch_id', $client->branch_id)->first();
       

        try {

            $response = $guzzle->post($client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $tokenAPI->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $webhookUpdatedData
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
