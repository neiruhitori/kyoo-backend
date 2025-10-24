<?php

namespace App\Mail\CS;

use Storage;
use Carbon\Carbon;
use App\Models\UserMobile;
use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;
use App\Models\AppointmentOnsite;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppointmentOnsiteCreatedMail extends Mailable implements ShouldQueue
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
        $branch = $this->appointmentOnsite->Service->Branch;
        $id_date = Carbon::parse($this->appointmentOnsite->date);
        $id_date->setLocale('id');
        config(['app.name' => $branch->name]);

        $clientApp = false;
        if ($this->appointmentOnsite->client_id) {
            $clientApp = UserMobile::where('id', $this->appointmentOnsite->client_id)->exists();
        }
        $url = $clientApp 
                ? URL::signedRoute('app.mobile.checkQueue', 
                        [
                            'branch_id' => $branch->id,
                            'booking_id' => $this->appointmentOnsite->id,
                        ])
                : url('customer/' . $branch->id. '/appointment-onsite/booking-status/' . $this->appointmentOnsite->id);

        $qr_code_url = 'qr_codes/'. $this->appointmentOnsite->id .'_qr_code.png';

        setlocale(LC_TIME, 'id_ID');

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Appointment Onsite di ' . $branch->name)
            ->markdown('emails.cs.storeAppointmentOnsite', [
                'appointment_onsite' => $this->appointmentOnsite,
                'appointment_onsite_id' => $this->appointmentOnsite->id,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'booking_code' => $this->appointmentOnsite->booking_code,
                'booking_day' => $id_date->isoFormat('dddd'),
                'booking_date' => $id_date->isoFormat('LL', 'id'),
                'start_time' => $this->appointmentOnsite->start_time,
                'end_time' => $this->appointmentOnsite->end_time,
                'service_name' => $this->appointmentOnsite->Service->name,
                'address' => $branch->address,
                'qr_code' => $qr_code_url,
                'url' => $url,
            ]);
    }
}
