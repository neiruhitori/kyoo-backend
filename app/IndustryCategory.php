<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustryCategory extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'icon', 'is_active'];
}
