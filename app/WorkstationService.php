<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkstationService extends Model
{
    protected $fillable = ['workstation_id', 'service_id', 'priority'];

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }

    public function Workstation()
    {
        return $this->belongsTo('App\Workstation');
    }

    /**
     * Get all of the WorkstationVct for the WorkstationService
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function WorkstationVct(): HasMany
    {
        return $this->hasMany(WorkstationVct::class, 'workstation_id', 'workstation_id');
    }
}
