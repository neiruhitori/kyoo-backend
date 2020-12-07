<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchType extends Model
{
    protected $fillable = ['code', 'name', 'isAppointment', 'isDirectQueue', 'isPremium'];
}
