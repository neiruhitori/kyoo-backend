<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'slot_id', 'booking_code', 'date', 'name', 'phone', 'email', 'notes', 'status',  'vct_id', 'checkin_time', 'served_time', 'rating', 'is_liked', 'end_served_time', 'number', 'appointment_channel', 'service_id', 'workstation_id', 'serving_duration', 'waiting_duration', 'branch_id'];

    protected $casts = [
        'number' => 'integer'
    ];

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', \strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', \strtotime($value));
    }

    public function Slot()
    {
        return $this->belongsTo('App\Slot')->orderBy('start_time', 'desc')->withTrashed();
    }

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }

    public function Workstation()
    {
        return $this->belongsTo('App\Workstation');
    }
}
