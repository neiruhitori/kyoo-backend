<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchType extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['code', 'name', 'is_premium', 'is_appointment', 'is_direct_queue', 'is_exhibition'];
}
