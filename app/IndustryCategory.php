<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndustryCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'icon', 'is_active'];
}
