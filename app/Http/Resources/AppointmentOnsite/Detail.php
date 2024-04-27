<?php

namespace App\Http\Resources\AppointmentOnsite;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\AppointmentOnsite;

class Detail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $branch = $this->Service->Branch;

        return [
            'id' => $this->id,
            'date' => $this->date,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'booking_code' => $this->booking_code,
            'service_id' => $this->service_id,
            'service_name' => $this->Service->name,
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'phone' => $this->phone,
            'emergency_number' => $this->emergency_number,
            'passport_number' => $this->passport_number,
            'email' => $this->email,
            'reason_for_visit' => $this->reason_for_visit,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_used' => $this->is_used,
            'direct_queue_id' => $this->DirectQueue ? $this->DirectQueue->id : null,
        ];
    }
}
