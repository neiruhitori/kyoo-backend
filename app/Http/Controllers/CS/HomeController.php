<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\Log;
use App\Slot;
use App\ScheduleTemplateDetail;
use App\Schedule;
use App\Service;
use App\Http\Requests\CS\StoreAppointment;
use App\Mail\CS\StoreAppointment as StoreAppointmentMail;
use Auth;
use Mail;

class HomeController extends Controller
{
    private $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
        if (!Auth::user()->Branch->BranchType->is_appointment) {
            return redirect(route('cs.directQueue.monitor'));
        }
        $dateNow = date('Y-m-d');
        
        $appointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['book', 'check in', 'served'])->get()->sortBy(function($query){
            return $query->slot->start_time;
        });

        $historyAppointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['no show', 'end served'])->get()->sortBy(function($query){
            return $query->slot->start_time;
        });
        
        return view('cs.home', [
            'appointments' => $appointments,
            'historyAppointments' => $historyAppointments
        ]);
    }
    
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        switch ($request->status) {
            case 'check in':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'checkin_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to Check in'
                ]);       
                break;
            case 'served':
                $onServed = Appointment::where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('status', 'served')->first();
                if (isset($onServed)) {
                    $request->session()->flash('error', __('appointment.in_progres', ['service' => $appointment->Slot->Service->name]));
                    return redirect(route('cs.home'));
                }
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to Served'
                ]);    
                break;
            case 'end served':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'end_served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to End Served'
                ]);    
                break;
            case 'no show':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to No Show'
                ]);    
                break;
        }
        $request->session()->flash('success', __('Appointment #:no status has been changed', ['no' => $appointment->number]));
        return redirect(route('cs.home'));
    }

    public function createAppointment()
    {
        $services = Service::where('branch_id', Auth::user()->branch_id)->get();
        return view('cs.createAppointment', [
            'services' => $services
        ]);
    }

    public function storeAppointment(StoreAppointment $request)
    {
        // copy from App\Http\Controllers\API\AppointmentController.php store()
        /**
         * additional validations:
         * - user cant create appointment on different day with day on slot
         * - user cant create appointment when slot is full
         * - user cant create appointment on same time slot
         * - user cant create appointment on closed day with schedule template
         * - user cant create appointment on closed day
         * - user cant create appointment with past time slot for today
         */
        
        // cant create appointment on same time slot
        $sameAppointment = Appointment::where(['email' => $request->email])
                                            ->where(['phone' => $request->phone]) 
                                            ->where(['slot_id' => $request->slot_id]) 
                                            ->where(['date' => $request->date])
                                            ->first(); 
        if ($sameAppointment) {
            $request->session()->flash('error', __('Can not create the same appointment at the same time'));
            return back()->withInput();
        }

        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $selected_day = strtolower(date('l', strtotime($request->date)));
        $slot = Slot::find($request->slot_id);

        // cant create appointment when slot is full
        $sameAppointment = Appointment::where(['slot_id' => $request->slot_id]) 
                                            ->where(['date' => $request->date])
                                            ->count(); 

        if ($sameAppointment >= $slot->max_slots) {
            $request->session()->flash('error', "Appointment maximum limit already reached, please find other timeslot schedule");
            return back()->withInput();
        }

        // user cant create appointment on different day with day on slot
        if ($selected_day != $slot->day) {
            $request->session()->flash('error', "This service slot open at $slot->day");
            return back()->withInput();
        }

        // cant create appointment on closed day by schedule template
        if($slot->Service->Branch->schedule_template_id){
            $schedule_template_details = ScheduleTemplateDetail::where('schedule_template_id', $slot->Service->Branch->schedule_template_id)->where('date', $request->date)->first();
            if($schedule_template_details){
                $request->session()->flash('error', __('Service Provider Already Closed'));
                return back()->withInput();
            }
        }

        // cant create appointment on closed day
        $slot_day = Schedule::where('branch_id', $slot->Service->branch_id)->where('day', $selected_day)->get(['day', 'status'])->first();
        if ($slot_day->status == 'closed') {
            $request->session()->flash('error', __('Service Provider Already Closed'));
            return back()->withInput();
        }

        // cant create appointment with past time slot
        if ($request->date == $current_date && $slot->end_time < $current_time) {
            $request->session()->flash('error', __('Service Provider Already Closed'));
            return back()->withInput();
        }

        $input = $request->all();
        $input['booking_code'] = $this->generate_booking_code($this->permitted_chars, 5);
        $input['number'] = Appointment::whereDateAndSlotId($request->date, $request->slot_id)->get()->count() + 1;
        $input['appointment_channel'] = 'VCT web';
        $appointment = Appointment::create($input);

        // send email to customer
        Mail::to($request->email)->send(new StoreAppointmentMail($appointment));

        $request->session()->flash('success', __('Appointment has been inserted'));
        return redirect(route('cs.appointment.create'));
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
}
