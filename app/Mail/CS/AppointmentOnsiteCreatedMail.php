<?php

namespace App\Mail\CS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\AppointmentOnsite;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;

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
        $branch = $this->appointmentOnsite->Service->Branch;
        $id_date = Carbon::parse($this->appointmentOnsite->date);
        $id_date->setLocale('id');
        config(['app.name' => $branch->name]);

        setlocale(LC_TIME, 'id_ID');

        $qrCodeValue = strtoupper($this->appointmentOnsite->booking_code);
        $qrCode = QrCode::format('png')
                    ->size(200)->errorCorrection('H')
                    ->generate($qrCodeValue);
        $qrCodePath = 'qr_codes/'. $this->appointmentOnsite->id .'_qr_code.png';

        if (Storage::disk('local')->exists("public/{$qrCodePath}")) {
            Storage::disk('local')->delete("public/{$qrCodePath}");
        }

        Storage::disk('local')->put("public/{$qrCodePath}", $qrCode);

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
                'qr_code' => $qrCodePath,
            ]);
    }
}
