<?php

namespace App\Notifications;

use App\Models\AppointmentOnsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;

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
        $qrCodeValue = strtoupper($appointmentOnsite->booking_code);
        $qrCode = QrCode::format('png')
                    ->size(200)->errorCorrection('H')
                    ->generate($qrCodeValue);
        $qr_code_url = 'qr_codes/'. $appointmentOnsite->id .'_qr_code.png';

        if (Storage::disk('local')->exists("public/{$qr_code_url}")) {
            Storage::disk('local')->delete("public/{$qr_code_url}");
        }

        Storage::disk('local')->put("public/{$qr_code_url}", $qrCode);

        Http::withHeaders([
            'Authorization' => $appointmentOnsite->Service->Branch->BranchConfiguration->api_token,
            'content-type' => 'application/json',
        ])
        ->post("{$appointmentOnsite->Service->Branch->BranchConfiguration->api_wa}/api/v1/sendTemplateMessages", [
            'broadcast_name' => 'kyoo_appt_qr_in',
            'template_name' => 'kyoo_appt_qr_in',
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
                        [
                            'name' => 'qr_code',
                            'value' => config('app.url') . '/storage/' . $qr_code_url,
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
