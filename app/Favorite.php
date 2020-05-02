<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'service_id'];

    public function Service()
    {
        return $this->belongsTo('App\Service');
    }
}
