<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeEmail extends Model
{
    protected $fillable = ['user_id', 'email'];
}
