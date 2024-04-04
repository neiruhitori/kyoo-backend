<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exhibition;
use App\Log;
use App\Slot;
use App\ScheduleTemplateDetail;
use App\Schedule;
use App\Service;
use App\Http\Requests\CS\StoreAppointment;
use App\Mail\CS\StoreExhibitionMail;
use App\Interfaces\ExhibitionRepositoryInterface;
use App\Models\BranchScheduleTemplateDetail;
use App\Models\CounterActivity;
use Illuminate\Support\Facades\Mail;
use App\Services\AppointmentService;
use Auth;
use App\User;
use App\Workstation;
use App\WorkstationService;
use App\WorkstationVct;

class HomeController extends Controller
{
    private $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected ExhibitionRepositoryInterface $exhibitionRepository;
    protected AppointmentService $appointmentService;

    public function __construct(
        ExhibitionRepositoryInterface $exhibitionRepository,
        AppointmentService $appointmentService
    )
    {
        $this->exhibitionRepository = $exhibitionRepository;
        $this->appointmentService = $appointmentService;
    }

    private function generate_booking_code($input, $strength = 5) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public function index()
    {
        $dateNow = date('Y-m-d');

        if (Auth::user()->Branch->BranchType->is_direct_queue) {
            return redirect(route('cs.directQueue.monitor'));
        }

        if (Auth::user()->Branch->BranchType->is_appointment) {
            return redirect()->route('cs.appointments.monitor');
        }

        if (Auth::user()->Branch->BranchType->is_exhibition) {
            $params = [
                'date' => $dateNow,
                'branch_id' => Auth::user()->branch_id
            ];

            $unfinishedQueue = $this->exhibitionRepository->getUnfinishedQueue($params);
            $finishedQueue = $this->exhibitionRepository->getFinishedQueue($params);

            return view('cs.homeExhibition', [
                'unfinishedQueue' => $unfinishedQueue,
                'finishedQueue' => $finishedQueue
            ]);
        }
    }

    public function createAppointment()
    {
        $workstationId = Auth::user()->WorkstationVct->workstation_id;

        $serviceIds = WorkstationService::where('workstation_id', $workstationId)
            ->get()
            ->map(function ($ws) {
                return $ws->service_id;
            });

        $services = Service::whereIn('id', $serviceIds)->get();

        return view('cs.createAppointment', [
            'services' => $services
        ]);
    }

    public function storeAppointment(StoreAppointment $request)
    {
        $slot = Slot::find($request->slot_id);

        $data = $request->all();
        $data['branch_id'] = $slot->Service->Department->branch_id;
        $data['service_id'] = $slot->service_id;

        try {
            $this->appointmentService->create($data);

            $request->session()->flash('success', __('Appointment has been inserted'));
            return redirect(route('cs.appointments.monitor'));
        } catch (\Throwable $e) {
            $request->session()->flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function createExhibition()
    {
        $services = Service::where('branch_id', Auth::user()->branch_id)->get();
        return view('cs.createExhibition', [
            'services' => $services
        ]);
    }

    public function storeExhibition(Request $request)
    {
        $branch = Slot::find($request->slot_id)->Service->Branch;

        $total_current_booking = Exhibition::where('date', $request->date ?? date('Y-m-d'))
            ->whereHas('Slot.Service', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->count();

        if (!$branch->BranchType->is_premium && $total_current_booking >= 200) {
            $request->session()->flash('error', 'Jumlah antrian melebihi batas maksimal harian untuk cabang berlisensi gratis');
            return back()->withInput();
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'slot_id' => 'required|exists:slots,id',
            'date' => 'required|date|after_or_equal:today',
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'notes' => 'nullable'
        ]);

        $sameQueue = Exhibition::where(['email' => $request->email])
            ->where(['phone' => $request->phone])
            ->where(['slot_id' => $request->slot_id])
            ->where(['date' => $request->date])
            ->first();
        if ($sameQueue) {
            $request->session()->flash('error', __('Can not create the same queue at the same time'));
            return back()->withInput();
        }

        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $selected_day = strtolower(date('l', strtotime($request->date)));
        $slot = Slot::find($request->slot_id);

        // cant create queue when slot is full
        $usedSlots = Exhibition::where(['slot_id' => $request->slot_id])
            ->where(['date' => $request->date])
            ->count();

        if ($usedSlots >= $slot->max_slots) {
            $request->session()->flash('error', __("Queue maximum limit already reached, please find other timeslot schedule"));
            return back()->withInput();
        }

        // user cant create queue on different day with day on slot
        if ($selected_day != $slot->day) {
            $request->session()->flash('error', __("This service open at :day", ['day' => $slot->day]));
            return back()->withInput();
        }

        // cant create queue on closed day by schedule template
        $holiday = BranchScheduleTemplateDetail::where([
            'branch_id' => $branch->id,
            'date' => $request->date
        ])->first();

        if ($holiday) {
            $request->session()->flash('error', __('Service Provider Already Closed'));
            return back()->withInput();
        }

        // cant create queue on closed day
        $slot_day = Schedule::where('branch_id', $slot->Service->branch_id)
            ->where('day', $selected_day)
            ->get(['day', 'status'])
            ->first();
        if ($slot_day->status == 'closed') {
            $request->session()->flash('error', __('Service Provider Already Closed'));
            return back()->withInput();
        }

        // cant create queue with past time slot
        if ($request->date == $current_date && $slot->end_time < $current_time) {
            $request->session()->flash('error', __('Service Provider Already Closed'));
            return back()->withInput();
        }

        $input = $request->all();
        $input['booking_code'] = $this->generate_booking_code($this->permitted_chars, 5);
        $input['queue_order'] = Exhibition::whereDateAndSlotId($request->date, $request->slot_id)->get()->count() + 1;
        $input['channel'] = 'VCT web';
        $input['service_id'] = $slot->service_id;

        $queue = Exhibition::create($input);

        // send email to customer
        Mail::to($request->email)
            ->send(new StoreExhibitionMail($queue));

        $request->session()
            ->flash('success', __('Queue has been inserted'));
        return redirect(route('cs.exhibition.create'));
    }

    public function updateExhibition(Request $request, Exhibition $exhibition)
    {
        switch ($request->status) {
            case 'end served':
                $exhibition->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'end_served_time' => date(now())
                ]);

                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update exhibition to End Served'
                ]);
                break;

            case 'no show':
                $exhibition->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'served_time' => date(now())
                ]);

                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update exhibition to No Show'
                ]);
                break;
        }

        $request->session()->flash('success', __('Queue #:no status has been changed', ['no' => $exhibition->queue_order]));
        return redirect(route('cs.home'));
    }

    public function qr()
    {
        $json_barcode = json_encode([
                'type' => 'show_branch_action',
                'branch' => [
                    'id' => Auth::user()->branch_id
                ]
            ]);

        $barcode = base64_encode($json_barcode);
        $image = \QrCode::format('png')
                         ->size(500)->errorCorrection('H')
                         ->generate($barcode);
      return response($image)->header('Content-type','image/png');
    }

    public function workstation()
    {
        $user = Auth::user();
        $workstations = collect();
        $userDepartments = $user->Branch->Departments->pluck('id');

        foreach ($userDepartments as $departmentId) {
            $workstations = $workstations->merge(Workstation::where('department_id', $departmentId)->get());
        }

        foreach ($workstations as $workstation) {
            $counter_activity = CounterActivity::where([
                'date' => date('Y-m-d'),
                'workstation_id' => $workstation->id
            ])
            ->whereNotNull('last_login')
            ->latest()
            ->first();

            $vct = null;
            if($counter_activity) {
                $vct = User::find($counter_activity->vct_id);
            }

            $workstation->vct_id = $counter_activity ? $counter_activity->vct_id : null;
            $workstation->vct_name = $vct ? $vct->name : null;
        }

        $workstations = $workstations->sortBy('name');

        return view('cs.workstation')->withUser($user)->withWorkstations($workstations);
    }

    public function updateWorkstation(User $user, Request $request)
    {
        $existing_workstation = WorkstationVct::where('vct_id', $user->id)->first();
        if ($existing_workstation) {
            WorkstationVct::where('vct_id', $user->id)->update([
                'workstation_id' => $request->workstation_id
            ]);
        } else {
            WorkstationVct::create([
                'vct_id' => $user->id,
                'workstation_id' => $request->workstation_id
            ]);
        }

        $this->updateVctActivity();

        return redirect()->route('cs.workstation')->with('success', __('Workstation Service has been updated'));
    }

    private function updateVctActivity()
    {
        CounterActivity::where([
            'date' => date('Y-m-d'),
            'vct_id' => Auth::id()
        ])
        ->whereNotIn('workstation_id', [Auth::user()->WorkstationVct->workstation_id])
        ->update([
            'last_login' => null
        ]);

        $activity = CounterActivity::where([
            'date' => date('Y-m-d'),
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
            'vct_id' => Auth::id()
        ])->first();

        $operationDuration = env('SESSION_LIFETIME') * 60;

        if ($activity) {
            $operationDuration += $activity->operation_duration;
            $activity->update([
                'operation_duration' => $operationDuration,
                'last_login' => date('Y-m-d H:i:s')
            ]);
        } else {
            $activity = CounterActivity::create([
                'date' => date('Y-m-d'),
                'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
                'vct_id' => Auth::id(),
                'operation_duration' => $operationDuration,
                'last_login' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
