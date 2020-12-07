<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchType extends Model
{
    protected $fillable = ['code', 'name', 'is_premium', 'is_appointment', 'is_direct_queue'];
}
