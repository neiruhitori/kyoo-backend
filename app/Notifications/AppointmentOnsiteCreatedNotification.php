<?php

namespace App\Notifications;

use App\Models\AppointmentOnsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AppointmentOnsiteCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsAppOfficial::class];
    }

    public function toWhatsApp(AppointmentOnsite $appointmentOnsite)
    {
        Http::withHeaders([
            'Authorization' => $appointmentOnsite->Service->Branch->BranchConfiguration->api_token,
            'content-type' => 'application/json',
        ])
        ->post("{$appointmentOnsite->Service->Branch->BranchConfiguration->api_wa}/api/v1/sendTemplateMessages", [
            'broadcast_name' => 'kyoo_appointment_in',
            'template_name' => 'kyoo_appointment_in',
            'receivers' => [
                [
                    'customParams' => [
                        [
                            'name' => 'patient_name',
                            'value' => $appointmentOnsite->name,
                        ],
                        [
                            'name' => 'appt_date',
                            'value' => $appointmentOnsite->date,
                        ],
                        [
                            'name' => 'appt_time',
                            'value' => "{$appointmentOnsite->start_time} - {$appointmentOnsite->end_time}",
                        ],
                        [
                            'name' => 'appt_type',
                            'value' => $appointmentOnsite->Service->name,
                        ],
                        [
                            'name' => 'appt_location',
                            'value' => $appointmentOnsite->Service->Branch->address,
                        ],
                        [
                            'name' => 'booking_url',
                            'value' => config('app.url') . "/customer/{$appointmentOnsite->Service->Branch->id}/appointment-onsite/booking-status/{$appointmentOnsite->id}",
                        ],
                        [
                            'name' => 'appt_code',
                            'value' => strtoupper($appointmentOnsite->booking_code),
                        ],
                    ],
                    'whatsappNumber' => $appointmentOnsite->phone,
                ],
            ],
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
