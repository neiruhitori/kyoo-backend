<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkstationVct extends Model
{
    protected $fillable = ['workstation_id', 'vct_id'];

    public function Workstation()
    {
        return $this->belongsTo('App\Workstation');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'vct_id', 'id');
    }
    

}
