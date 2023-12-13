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
            'phone' => $this->phone,
            'email' => $this->email,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
