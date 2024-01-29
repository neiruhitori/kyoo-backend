<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use App\Service;
use App\Listeners\SendAppointmentOnsiteCreatedNotification;

class AppointmentOnsite extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'fcm_id', 'client_id', 'booking_code', 'service_id',  'start_time', 'end_time', 'date', 'is_used'];

    public function Service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function sendAppointmentOnsiteCreatedNotification($appointmentOnsite)
    {
        $this->notify(new SendAppointmentOnsiteCreatedNotification($appointmentOnsite));
    }
}
