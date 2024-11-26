<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use App\Appointment;
use App\Mail\CS\AppointmentCreatedMail;
use App\Notifications\WhatsAppChannel;
use Illuminate\Support\Facades\Http;

class AppointmentCreatedNotification extends Notification implements ShouldQueue
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
        return ['mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(Appointment $appointment): AppointmentCreatedMail
    {
        return (new AppointmentCreatedMail($appointment))->to($appointment->email);
    }
    public function waBlast(Appointment $appointment){
        $branch = $appointment->Service->Branch;
        $type = $branch->getQueueTypeAttribute();
        //url dynamic to detail branch, example:https://dev.kyoo.id/customer/93/onsite/detail
        $url = url('/customer/'.$branch->id.'/'.$type.'/detail');

        // Data JSON yang akan dikirim
        $payload = [
            "phone_number"   => $appointment->phone,
            "name"           => $appointment->name,
            "branch_name"    => $branch->name,
            "booking_code"   => strtoupper($appointment->booking_code),
            "appointment_date" => $appointment->date,
            "start_time"     => $appointment->start_time,
            "end_time"       => $appointment->end_time,
            "service_name"   => $appointment->Service->name,
            "address"        => $branch->address,
            "branch_id"      => $branch->id,
            "id"             => $appointment->id,
            "link_branch"    => $url,
        ];
    
        // Mengirim request POST ke endpoint waBlast
        $response = Http::withHeaders([
            'x-api-key' => $branch->BranchConfiguration->api_token,
            'Content-Type' => 'application/json',
        ])->post('https://api.pawarta.awandigital.id/api/send-message-template', $payload);
    
    }

    public function toWhatsApp(Appointment $appointment): string
    {
        $url = url("customer/{$appointment->branch_id}/appointment/booking-status/{$appointment->id}");

        return "KYOO - Hai {$appointment->name}, nomor antrian Anda: {$appointment->number}. Cek antrian di: {$url}";
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
