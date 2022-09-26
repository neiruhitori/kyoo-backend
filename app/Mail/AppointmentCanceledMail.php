<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Appointment;

class AppointmentCanceledMail extends Mailable
{
    use Queueable, SerializesModels;

    private $appointment;

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
        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Appointment di ' . $this->appointment->Branch->name . ' Dibatalkan')
            ->markdown('appointments.emails.appointmentCanceled', [
                'appointment' => $this->appointment,
                'appointmentStatusURL' => url("customer/{$this->appointment->branch_id}/appointment/booking-status/{$this->appointment->id}")
            ]);
    }
}
