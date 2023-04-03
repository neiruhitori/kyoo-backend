<?php

namespace App\Mail\CS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Appointment;

class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private Appointment $appointment;

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
        $branch = $this->appointment->Slot->Service->Branch;

        setlocale(LC_TIME, 'id_ID');

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Appointment di ' . $branch->name)
            ->markdown('emails.cs.storeAppointment', [
                'appointment' => $this->appointment,
                'appointment_id' => $this->appointment->id,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'booking_date' => date('j F Y', strtotime($this->appointment->date))
            ]);
    }
}
