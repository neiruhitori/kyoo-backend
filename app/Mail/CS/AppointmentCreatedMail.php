<?php

namespace App\Mail\CS;

use Carbon\Carbon;
use App\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $country = $branch->country;
        $locale = $country == 'Indonesia' ? 'id' : 'en';
        // setlocale(LC_TIME, 'id_ID');
        app()->setLocale($locale);

        $startTime = Carbon::parse($this->appointment->date)->setTime(10, 0)->format('Ymd\THis');
        $endTime = Carbon::parse($this->appointment->date)->setTime(11, 0)->format('Ymd\THis');

        // Buat isi file ICS
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//KYOO//Appointment//EN\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:" . uniqid() . "@kyoo.id\r\n";
        $icsContent .= "DTSTAMP:" . now()->format('Ymd\THis') . "Z\r\n";
        $icsContent .= "DTSTART:{$startTime}Z\r\n";
        $icsContent .= "DTEND:{$endTime}Z\r\n";
        $icsContent .= "SUMMARY:Appointment at " . $branch->name . "\r\n";
        $icsContent .= "DESCRIPTION:Your scheduled appointment at " . $branch->name . "\r\n";
        $icsContent .= "LOCATION:" . $branch->address . "\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        // Simpan file ICS ke storage sementara
        $icsPath = storage_path('app/public/calendar/appointment-' . $this->appointment->id . '.ics');
        file_put_contents($icsPath, $icsContent);

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject(__('Appointment at :branch_name',['branch_name' => $branch->name]))
            ->markdown('emails.cs.storeAppointment', [
                'appointment' => $this->appointment,
                'appointment_id' => $this->appointment->id,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'booking_date' =>  Carbon::parse($this->appointment->date)->locale($locale)->isoFormat('D MMMM YYYY')
            ])
            ->attach($icsPath, [
                'as' => 'appointment.ics',
                'mime' => 'text/calendar',
            ]);
    }
}
