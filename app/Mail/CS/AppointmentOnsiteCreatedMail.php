<?php

namespace App\Mail\CS;

use App\Models\AppointmentOnsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentOnsiteCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private AppointmentOnsite $appointmentOnsite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AppointmentOnsite $appointmentOnsite)
    {
        $this->appointmentOnsite = $appointmentOnsite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $branch = $this->appointmentOnsite->Slot->Service->Branch;

        setlocale(LC_TIME, 'id_ID');

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Appointment Onsite di ' . $branch->name)
            ->markdown('emails.cs.storeAppointmentOnsite', [
                'appointment_onsite' => $this->appointmentOnsite,
                'appointment_onsite_id' => $this->appointmentOnsite->id,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'booking_date' => date('j F Y', strtotime($this->appointmentOnsite->date))
            ]);
    }
}
