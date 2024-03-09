<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workstation extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'name', 'label', 'display_id'];

    public function Department()
    {
        return $this->belongsTo('App\Department');
    }

    public function WorkstationService()
    {
        return $this->hasMany('App\WorkstationService')->orderBy('priority', 'ASC');
    }

    public function WorkstationVct()
    {
        return $this->hasMany('App\WorkstationVct');
    }

    public function DirectQueues()
    {
        return $this->hasMany(DirectQueue::class, 'workstation_id', 'id');
    }
}
