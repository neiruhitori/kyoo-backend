<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function User()
    {
        return $this->belongsTo('App\User');
    }
}
