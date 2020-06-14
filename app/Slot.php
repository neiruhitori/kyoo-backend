<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slot extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['service_id', 'max_slots', 'day', 'start_time', 'end_time'];

    public function getStartTimeAttribute($value)
    {
        return date("H:i", strtotime($value));
    }

    public function getEndTimeAttribute($value)
    {
        return date("H:i", strtotime($value));
    }

    public function Service()
    {
        return $this->belongsTo('App\Service')->withTrashed();
    }
}
