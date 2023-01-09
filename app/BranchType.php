<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'is_premium', 'is_appointment', 'is_direct_queue', 'is_exhibition'];
}
