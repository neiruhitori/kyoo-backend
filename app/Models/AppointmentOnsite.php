<?php

namespace App\Models;

use App\Notifications\AppointmentOnsiteCreatedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Service;

class AppointmentOnsite extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'fcm_id', 'client_id', 'booking_code', 'service_id',  'start_time', 'end_time', 'date', 'is_used', 'slot_id', 'date_of_birth', 'address', 'emergency_number', 'passport_number', 'reason_for_visit'];

    public function Service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function Slot(): BelongsTo
    {
        return $this->belongsTo('App\Slot')->orderBy('start_time', 'desc');
    }

    public function DirectQueue()
    {
        return $this->hasOne('App\DirectQueue', 'appointment_onsite_id', 'id');
    }

    public static function sendAppointmentOnsiteCreatedNotification($appointmentOnsite)
    {
        $notification = new AppointmentOnsiteCreatedNotification();
        $notification->toWhatsApp($appointmentOnsite);
    }

    public function getQrCodeAttribute()
    {
        return 'qr_codes/' . $this->id . '_qr_code.png';
    }
}
