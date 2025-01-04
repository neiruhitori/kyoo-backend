<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Branch;
use Carbon\Carbon;
use App\DirectQueue;
use App\WorkstationService;
use Illuminate\Http\Request;
use App\Models\WebkioskToken;
use App\Models\AppointmentOnsite;
use App\Events\OnsiteQueueUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\WebkioskConfiguration;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\Device\StoreDirectQueue;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;

class KioskController extends Controller
{
    private DirectQueueRepositoryInterface $onsite_repository;

    public function __construct(
        DirectQueueRepositoryInterface $onsite_repository
    )
    {
        $this->onsite_repository = $onsite_repository;
    }
    public function login(Request $request)
    {
            $user = User::where('username', $request->username)
                    ->where('role','device')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Data pengguna atau password tidak sesuai.',
                'status_code' => 400
            ], 400);
        }

        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken('authToken')->accessToken,
            'token_type' => 'Bearer',
            'status_code' => 200,
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json('Successfully Logout');
    }

    public function getWebkioskUI()
    {
        $branch_id = Auth::user()->branch_id;
        $configuration = WebkioskConfiguration::where('branch_id', $branch_id)
                        ->with('layoutConfiguration2')
                        ->with('layoutConfiguration3')
                        ->with('layoutConfiguration4')
                        ->with('layout')
                        ->first();
        $branch = Branch::findOrFail($branch_id);
        $WebkioskConfigurationID = $branch->WebkioskConfiguration->id;
        $WebKioskToken = WebkioskToken::where('webkiosk_configuration_id', $WebkioskConfigurationID)->first();

       return response()->json([
            'user' => Auth::user()->name,
            'role' => Auth::user()->role,
            'config' => $configuration,
            'webkiosk_token' => $WebKioskToken,
       ]);
    }

    public function getService()
    {
        $branch_id = Auth::user()->branch_id;
        $workstationServices = WorkstationService::whereHas('Workstation.WorkstationVct', function ($query) use ($branch_id) {
            $query->whereIn('vct_id', function ($subquery) use ($branch_id) {
                $subquery->select('id')
                    ->from('users')
                    ->where('branch_id', $branch_id)
                    ->whereIn('role', ['cs', 'spv']);
            });
        })->with('Service')->get();
        
        // Hilangkan duplikasi berdasarkan `service_id`
        $uniqueServices = collect($workstationServices)->unique('service_id')->values();
        
        return response()->json([
            'success' => true,
            'message' => 'get all service on branch',
            'data' => $uniqueServices,
        ]);
    }
    public function store(StoreDirectQueue $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            $workstation_service = WorkstationService::find($request->workstation_service_id);

            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $data['service_id'] = $workstation_service->service_id;
            $data['phone'] = null;
            $data['direct_queue_channel'] = 'Device';

            $direct_queue = $this->onsite_repository->store($data);
            $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                                                        ->whereStatus('waiting')
                                                        ->whereDate('created_at', date('Y-m-d'))
                                                        ->count();

            event(new VCTDirectQueueEvent($direct_queue, $user->branch_id));
            event(new DirectQueueEvent($direct_queue, $user->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            return response()->json([
                'success' => true,
                'data' => $direct_queue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 412);
        }
    }

    public function checkIn(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);

            $branchConfig = $user->branch->branchConfiguration;

            $checked_in_appointment = AppointmentOnsite::where('booking_code', strtolower($request->booking_code))
            ->where('is_used', true)
            ->whereDate('date', date('Y-m-d'))
            ->first();

            if($checked_in_appointment) {
                $direct_queue = DirectQueue::where('appointment_onsite_id', $checked_in_appointment->id)->first();
                $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                ->whereStatus('waiting')
                ->whereDate('created_at', date('Y-m-d'))
                ->where('created_at', '<=', $direct_queue->created_at)
                ->count();

                return response()->json([
                    'success' => true,
                    'data' => $direct_queue
                ]);
            }

            $appointment_onsite = AppointmentOnsite::where('booking_code', strtolower($request->booking_code))
            ->where('is_used', false)
            ->whereDate('date', '>=', date('Y-m-d'))
            ->first();
            

            if(!$appointment_onsite) {
                throw new \Exception('Kode Booking tidak ditemukan atau sudah tidak berlaku', 10003);
            } elseif ($appointment_onsite->date != date('Y-m-d')) {
                throw new \Exception('Kode booking belum berlaku, silahkan cek tanggal booking.', 10004);
            }

            if ($branchConfig->check_in_rule != 0) {
                $startTime =  Carbon::createFromFormat('H:i:s', $appointment_onsite->start_time);

                $allowedCheckInTime = $startTime->subHours($branchConfig->check_in_rule);
    
                if (now()->format('H:i:s') < $allowedCheckInTime->format('H:i:s')) {
                    $message = "Check-in dilakukan " . $branchConfig->check_in_rule . " jam sebelum layanan buka. Anda dapat check-in pada jam " . $allowedCheckInTime->format('H:i') . ".";
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 412);
                    // throw new \Exception("Check-in dilakukan " . $branchConfig->check_in_rule . " jam sebelum layanan buka. Anda dapat check-in pada jam " . $allowedCheckInTime->format('H:i') . ".", 10005);
                }
            }

            $data = $appointment_onsite->toArray();
            // $data['vct_id'] = $request->vct_id;
            $data['user_id'] = $user->id;
            $data['direct_queue_channel'] = 'Device';
            $data['priority'] = 1;
            $data['appointment_onsite_id'] = $appointment_onsite->id;

            $direct_queue = $this->onsite_repository->store($data);
            $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                                                        ->whereStatus('waiting')
                                                        ->whereDate('created_at', date('Y-m-d'))
                                                        ->count();

            event(new VCTDirectQueueEvent($direct_queue, $user->branch_id));
            event(new DirectQueueEvent($direct_queue, $user->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            $appointment_onsite->update([
                'is_used' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $direct_queue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 412);
        }
    }
}
