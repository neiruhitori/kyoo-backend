<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkstationService extends Model
{
    protected $fillable = ['workstation_id', 'service_id', 'priority'];

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }
}
