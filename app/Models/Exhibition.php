<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    protected $fillable = ['user_id', 'slot_id', 'booking_code', 'date', 'name', 'phone', 'email', 'notes', 'status', 'queue_order', 'vct_id', 'rating', 'is_liked', 'end_served_time', 'number', 'channel'];

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
}
