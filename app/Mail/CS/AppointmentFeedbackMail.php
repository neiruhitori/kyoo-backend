<?php

namespace App\Mail\CS;

use Storage;
use Carbon\Carbon;
use App\Appointment;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppointmentFeedbackMail extends Mailable implements ShouldQueue
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
        $branch = $this->appointment->Branch;
        config(['app.name' => $branch->name]);

        $full = URL::signedRoute(
                            'feedback.mail',
                            [
                                'branchId' => $branch->id,
                                'queueType' => 'appointment',
                                'queueId' => $this->appointment->id
                            ]
                        );
        $survey = parse_url($full, PHP_URL_PATH) . '?' . parse_url($full, PHP_URL_QUERY);

        setlocale(LC_TIME, 'id_ID');

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Give us your feedback in ' . $branch->name)
            ->markdown('emails.cs.feedbackMail', [
                'survey_link' => $survey,
                'branch_name' => $branch->name,
            ]);
    }
}
