<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\AppointmentOnsite;
use App\Jobs\AppointmentOnsiteMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\CS\AppointmentOnsiteCreatedMail;
use Illuminate\Notifications\Messages\MailMessage;

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

    public function waBlast(AppointmentOnsite $appointmentOnsite)
    {
        $branch = $appointmentOnsite->Service->Branch;
        $type = $branch->getQueueTypeAttribute();
        //url dynamic to detail branch, example:https://dev.kyoo.id/customer/93/onsite/detail
        $url = url('/customer/' . $branch->id . '/' . $type . '/detail');
        $url_booking = url('/customer/' . $branch->id . '/' . $type . '/booking-status/' . $appointmentOnsite->id);

        // Data JSON yang akan dikirim
        $payload = [
            "phone_number" => $appointmentOnsite->phone,
            "name" => $appointmentOnsite->name,
            "branch_name" => $branch->name,
            "booking_code" => strtoupper($appointmentOnsite->booking_code),
            "appointment_date" => $appointmentOnsite->date,
            "start_time" => $appointmentOnsite->start_time,
            "end_time" => $appointmentOnsite->end_time,
            "service_name" => $appointmentOnsite->Service->name,
            "address" => $branch->address,
            "booking_status" => $url_booking,
            "link_branch" => $url,
        ];

        // Mengirim request POST ke endpoint waBlast
        $response = Http::withHeaders([
            'x-api-key' => $branch->BranchConfiguration->api_token,
            'Content-Type' => 'application/json',
        ])->post('https://api.pawarta.awandigital.id/api/send-message-template', $payload);

    }

    public function toWhatsApp(AppointmentOnsite $appointmentOnsite)
    {
        $senderPhone = null;

        Http::withHeaders([
            'Authorization' => $appointmentOnsite->Service->Branch->BranchConfiguration->api_token,
            'content-type' => 'application/json',
        ])
            ->post("{$appointmentOnsite->Service->Branch->BranchConfiguration->api_wa}/api/v1/sendTemplateMessages", [
                'broadcast_name' => $appointmentOnsite->Service->whatsapp_template ?? 'kyoo_appt_qr_in',
                'template_name' => $appointmentOnsite->Service->whatsapp_template ?? 'kyoo_appt_qr_in',
                'sender_phone' => $senderPhone ?? '',

                'receivers' => [
                    [
                        'customParams' => [
                            [
                                'name' => 'patient_name',
                                'value' => $appointmentOnsite->name,
                            ],
                            [
                                'name' => 'qr_code',
                                'value' => config('app.url') . '/storage/' . $appointmentOnsite->qr_code,
                            ],
                            [
                                'name' => 'appt_code',
                                'value' => strtoupper($appointmentOnsite->booking_code),
                            ],
                            [
                                'name' => 'appt_date',
                                'value' => date('d M Y', strtotime($appointmentOnsite->date)),
                            ],
                            [
                                'name' => 'appt_time',
                                'value' => $appointmentOnsite->start_time,
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
