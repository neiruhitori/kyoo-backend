<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['user_id', 'slot_id', 'date', 'name', 'phone', 'email', 'notes', 'status', 'rating', 'is_liked'];

    public function Slot()
    {
        return $this->belongsTo('App\Slot');
    }
}
