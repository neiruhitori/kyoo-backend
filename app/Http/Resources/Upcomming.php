<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Appointment as AppointmentModel;
use App\DirectQueue;
class Upcomming extends JsonResource
{
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'type' => $this['is_direct_queue'] ? 'direct_queue' : 'appointment',
            'appointment' => null,
            'direct_queue' => null,
        ];
        
        if ($this['is_direct_queue']) {
            $directQueue = DirectQueue::find($this['id']);
            $data['direct_queue'] = [
                'id' => $directQueue->id,
                'date' => $directQueue->created_at,
                'branch_id' => $directQueue->Service->Branch->id,
                'branch_name' => $directQueue->Service->Branch->name,
                'service_name' => $directQueue->Service->name,
                'total_waiting' => DirectQueue::whereWorkstationServiceId($directQueue->workstation_service_id)->whereStatus('waiting')->where('queue_no', '<', $directQueue->queue_no)->whereDate('created_at', date('Y-m-d'))->count(),
                'name' => $directQueue->name,
                'phone' => $directQueue->phone,
                'queue_no' => $directQueue->queue_no,
                'rating' => $directQueue->rating,
                'is_liked' => $directQueue->is_liked,
                'status' => $directQueue->status,
            ];
        }else{
            $appointment = AppointmentModel::find($this['id']);
            $currently_attending = AppointmentModel::select('number')->where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('status', 'served')->first();
            $data['appointment'] = [
                'id' => $appointment->id,
                'branch_id' => $appointment->Slot->Service->Branch->id,
                'branch_name' => $appointment->Slot->Service->Branch->name,
                'service_name' => $appointment->Slot->Service->name,
                'service_id' => $appointment->Slot->Service->id,
                'status' => $appointment->status,
                'date' => $appointment->date,
                'start_time' => $appointment->Slot->start_time,
                'end_time' => $appointment->Slot->end_time,
                'timezone' => $appointment->Slot->Service->Branch->timezone,
                'booking_code' => $appointment->booking_code,
                'industry_category' => $appointment->Slot->Service->Branch->IndustryCategory->name,
                'name' => $appointment->name,
                'phone' => $appointment->phone,
                'email' => $appointment->email,
                'rating' => $appointment->rating,
                'is_liked' => $appointment->is_liked,
                'queue_no' => (int) $appointment->number,
                'total_waiting' => AppointmentModel::where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('number', '<', $appointment->number)->whereIn('status', ['book', 'check in'])->get()->count(),
                'currently_attending' => isset($currently_attending) ? intval($currently_attending->number) : 0,
            ];
        }

        return $data;
    }
}
