<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotService extends Model
{
    use HasFactory;

    protected $fillable = ['slot_id', 'service_id', 'max_slots'];

    public function Slot()
    {
        return $this->belongsTo('App\Slot');
    }

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }
}
