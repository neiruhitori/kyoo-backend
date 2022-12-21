<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slot extends Model
{
    use SoftDeletes, HasFactory;
    
    protected $fillable = ['department_id', 'day', 'start_time', 'end_time'];

    public function getStartTimeAttribute($value)
    {
        return date("H:i", strtotime($value));
    }

    public function getEndTimeAttribute($value)
    {
        return date("H:i", strtotime($value));
    }

    public function Department()
    {
        return $this->belongsTo('App\Department');
    }

    public function SlotService()
    {
        return $this->hasMany('App\Models\SlotService');
    }
}
