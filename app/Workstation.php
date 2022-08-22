<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workstation extends Model
{
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
}
