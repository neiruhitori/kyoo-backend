<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['user_id', 'slot_id', 'booking_code', 'date', 'name', 'phone', 'email', 'notes', 'status',  'vct_id', 'checkin_time', 'served_time', 'rating', 'is_liked', 'end_served_time', 'number'];

    public function Slot()
    {
        return $this->belongsTo('App\Slot')->orderBy('start_time', 'desc')->withTrashed();
    }
}
