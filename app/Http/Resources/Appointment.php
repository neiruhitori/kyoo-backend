<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Appointment as AppointmentModel;

class Appointment extends JsonResource
{
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $currently_attending = AppointmentModel::select('number')->where('slot_id', $this->slot_id)->where('date', $this->date)->where('status', 'served')->first();
        return [
            'id' => $this->id,
            'branch_id' => $this->Slot->Service->Branch->id,
            'branch_name' => $this->Slot->Service->Branch->name,
            'service_name' => $this->Slot->Service->name,
            'service_id' => $this->Slot->Service->id,
            'status' => $this->status,
            'date' => $this->date,
            'start_time' => $this->Slot->start_time,
            'end_time' => $this->Slot->end_time,
            'timezone' => $this->Slot->Service->Branch->timezone,
            'booking_code' => $this->booking_code,
            'industry_category' => $this->Slot->Service->Branch->IndustryCategory->name,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'rating' => $this->rating,
            'is_liked' => $this->is_liked,
            'queue_no' => (int) $this->number,
            'total_waiting' => AppointmentModel::where('slot_id', $this->slot_id)->where('date', $this->date)->where('number', '<', $this->number)->whereIn('status', ['book', 'check in'])->get()->count(),
            'currently_attending' => isset($currently_attending) ? $currently_attending->number : 0
        ];
    }
}
