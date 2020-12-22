<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workstation extends Model
{
    use SoftDeletes;
    protected $fillable = ['department_id', 'name', 'label', 'display_id'];

    public function Department()
    {
        return $this->belongsTo('App\Department');
    }

    public function WorkstationService()
    {
        return $this->hasMany('App\WorkstationService')->orderBy('priority', 'DESC');
    }
}
