<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\AppointmentCreatedNotification;

class Appointment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'vct_id',
        'slot_id',
        'branch_id',
        'service_id',
        'workstation_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'number',
        'booking_code',
        'name',
        'phone',
        'email',
        'notes',
        'status',
        'checkin_time',
        'served_time',
        'end_served_time',
        'rating',
        'is_liked',
        'appointment_channel',
        'serving_duration',
        'waiting_duration',
        'survey_type',
        'client_id',
    ];

    protected $casts = [
        'vct_id' => 'integer',
        'slot_id' => 'integer',
        'branch_id' => 'integer',
        'service_id' => 'integer',
        'workstation_id' => 'integer',
        'user_id' => 'integer',
        'number' => 'integer',
        'status' => 'string',
        'rating' => 'integer',
        'is_liked' => 'boolean',
        'serving_duration' => 'integer',
        'waiting_duration' => 'integer'
    ];

    public function Branch()
    {
        return $this->belongsTo('App\Branch');
    }

    public function Slot()
    {
        return $this->belongsTo('App\Slot')->orderBy('start_time', 'desc');
    }

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }
    public static function sendNotificationWaBlast($appointment){
        $notification = new AppointmentCreatedNotification();
        $notification->waBlast($appointment);
    }

    public function Workstation()
    {
        return $this->belongsTo('App\Workstation');
    }

    public function scopeWithoutCanceled($query)
    {
        $query->where('status', '!=', 'canceled');
    }
}
