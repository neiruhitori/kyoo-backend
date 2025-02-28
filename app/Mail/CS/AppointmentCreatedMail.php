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

        $startTime = Carbon::parse($this->appointment->date . ' ' . $this->appointment->start_time)
        ->format('Ymd\THis');
        $endTime = Carbon::parse($this->appointment->date . ' ' . $this->appointment->end_time)
        ->format('Ymd\THis');

        // init file ics
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
            ->attachData($icsContent,'appointment-'.$this->appointment->id.'.ics' ,[
                'mime' => 'text/calendar',
            ]);
    }
}
