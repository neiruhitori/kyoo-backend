<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\StoreAppointment;
use App\Http\Requests\API\FeedbackAppointment;
use App\Appointment;
use App\Http\Resources\Appointment as AppointmentCollection;
use Auth;

class AppointmentController extends Controller
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

    public function store(StoreAppointment $request)
    {
        $input = $request->all();
        $input['booking_code'] = $this->generate_booking_code($this->permitted_chars, 5);
        $appointment = Appointment::create($input);

        return response()->json([
            'success' => true,
            'message' => 'create appointment',
            'data' => $appointment
        ]);
    }

    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())->where('status', 'book')->orderBy('date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'get all appointment',
            'data' => AppointmentCollection::collection($appointments)
        ]);
    }

    public function show(Appointment $appointment)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail appointment by id',
            'data' => new AppointmentCollection($appointment)
        ]);
    }
    
    public function history()
    {
        $appointments = Appointment::where('user_id', Auth::id())->where('status', '!=', 'book')->orderBy('date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'get all history appointment',
            'data' => AppointmentCollection::collection($appointments)
        ]);
    }

    public function feedback(FeedbackAppointment $request, Appointment $appointment)
    {
        $appointment->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'success give feedback appointment',
            'data' => $appointment
        ]);
    }

    public function upcoming()
    {
        $dateNow = date('Y-m-d');
        $appointments = Appointment::with('Slot.Service')->where('date', '>=', $dateNow)->where('user_id', Auth::id())->where('status', 'book')->orderBy('date', 'asc')->get();
        foreach ($appointments as $appointment) {
            $filledSlot = Appointment::whereHas('Slot', function($query) use ($appointment) {
                $query->where('id', $appointment->slot_id);
            })->where('date', $dateNow)->whereIn('status', ['book', 'check in'])->where('user_id', '!=', Auth::id())->get();
            $appointment['Slot']['waiting'] = count($filledSlot);
        }
        return response()->json([
            'success' => true,
            'message' => 'get upcoming appointment',
            'data' => $appointments
        ]);
    }
}
