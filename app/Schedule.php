<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['branch_id', 'day', 'start_time', 'end_time', 'status'];
}
