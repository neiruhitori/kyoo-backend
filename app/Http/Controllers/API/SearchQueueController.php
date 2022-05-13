<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use App\Appointment;
use App\Models\Exhibition;

class SearchQueueController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {
            $onsite = DirectQueue::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
                ->latest()
                ->first();
            if ($onsite) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => 'customer/' . $onsite->Service->branch_id . '/onsite/booking-status/' . $onsite->id
                    ]
                ]);
            }

            $appointment = Appointment::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
                ->latest()
                ->first();
            if ($appointment) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => 'customer/' . $appointment->Slot->Service->branch_id . '/appointment/booking-status/' . $appointment->id
                    ]
                ]);
            };

            $exhibition = Exhibition::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
                ->latest()
                ->first();
            if ($exhibition) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => 'customer/' . $exhibition->Slot->Service->branch_id . '/exhibition/booking-status/' . $exhibition->id
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak ditemukan'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
