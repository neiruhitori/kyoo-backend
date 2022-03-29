<?php

namespace App\Mail\CS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Appointment;
use Crypt;

class StoreAppointment extends Mailable
{
    use Queueable, SerializesModels;
    public $appointment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appointment_id = $this->appointment->id;
        $branch = $this->appointment->Slot->Service->Branch;

        return $this->from('noreply@kyoo.id', 'KYOO')->subject(__('Branch Appointment'))->markdown('emails.cs.storeAppointment', [
            'appointment' => $this->appointment,
            'appointment_id' => $appointment_id,
            'branch_id' => $branch->id
        ]);
    }
}
