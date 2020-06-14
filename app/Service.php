<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['branch_id', 'name'];

    public function Slot()
    {
        return $this->hasMany('App\Slot');
    }

    public function Branch()
    {
        return $this->belongsTo('App\Branch')->withTrashed();
    }
}
