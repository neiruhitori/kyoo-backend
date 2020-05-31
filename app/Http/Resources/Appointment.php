<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'booking_code' => $this->booking_code,
            'industry_category' => $this->Slot->Service->Branch->IndustryCategory->name,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'rating' => $this->rating,
            'is_liked' => $this->is_liked
        ];
    }
}
